<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use EmailQueue\EmailQueue;

/**
 * EmailDatabaseBackup command.
 */
class EmailDatabaseBackupCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Creates a DB backup and emails it to the support email address
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        // Load config file and consume DB credentials
        if (file_exists(CONFIG . 'environment.php')) {
            include(CONFIG . 'environment.php');
            $environment = getEnvironment();

            if (file_exists(CONFIG . 'app_local_' . $environment . '.php')) {
                Configure::load('app_local_' . $environment);
            }
        }
        $host = Configure::consume('Datasources.default.host');
        $user = Configure::consume('Datasources.default.username');
        $password = Configure::consume('Datasources.default.password');
        $databaseName = Configure::consume('Datasources.default.database');

        $filename = "db_backup_" . date("Ymd_His") . ".sql";
        $filepath = ROOT . DS . 'database_backups' . DS . $filename;

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --ignore-table=%s.email_queue %s > %s',
            $user,
            $password,
            $host,
            $databaseName,
            $databaseName,
            $filepath
        );

        $result = shell_exec($command);
        if ($result === null && file_exists($filepath) && filesize($filepath) > 0) {
            echo 'Database backup created successfully: ' . $filename . PHP_EOL;
            $this->emailDatabaseBackup($filepath);
        } else {
            $msg = 'Error creating database backup: ' . ($result ?? 'Unknown error');
            $msg .= match (true) {
                !file_exists($filepath) => "\n No backup file was created",
                filesize($filepath) === 0 => "\n Backup file is empty",
                default => ''
            };
            echo $msg . PHP_EOL;
            $this->emailError($msg);
        }
    }

    private function emailDatabaseBackup($filepath): void
    {
        EmailQueue::enqueue(
            Configure::read('supportEmail'),
            ['content' => 'Database backup attached'],
            [
                'subject' => 'Vore Arts Fund database backup',
                'template' => 'default',
                'from_name' => 'Vore Arts Fund',
                'from_email' => 'noreply@voreartsfund.org',
                'attachments' => [$filepath]
            ],
        );
    }

    private function emailError($msg)
    {
        EmailQueue::enqueue(
            Configure::read('supportEmail'),
            ['content' => $msg],
            [
                'subject' => 'Vore Arts Fund database backup failed',
                'template' => 'default',
                'from_name' => 'Vore Arts Fund',
                'from_email' => 'noreply@voreartsfund.org',
            ],
        );
    }
}
