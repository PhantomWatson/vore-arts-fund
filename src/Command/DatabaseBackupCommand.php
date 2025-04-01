<?php
declare(strict_types=1);

namespace App\Command;

use App\Alert\Alert;
use Aws\S3\S3Client;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use EmailQueue\EmailQueue;

/**
 * DatabaseBackup command.
 */
class DatabaseBackupCommand extends Command
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
        } else {
            $this->sendErrorAlert('Cannot back up database: environment.php not found.');
            return;
        }
        $host = Configure::consume('Datasources.default.host');
        $user = Configure::consume('Datasources.default.username');
        $password = Configure::consume('Datasources.default.password');
        $databaseName = Configure::consume('Datasources.default.database');

        $filename = sprintf('db_backup_%s_%s.sql', $environment, date('Y-m-d_H-i-s'));
        $filepath = ROOT . DS . 'database_backups' . DS . $filename;

        $command = sprintf(
            '%s --user=%s --password=%s --host=%s --ignore-table=%s.email_queue --no-tablespaces %s > %s',
            Configure::read('dbDumpCommand'),
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
            $this->uploadFileToS3($filepath);
        } else {
            $msg = 'Error creating database backup: ' . ($result ?? 'Unknown error');
            $msg .= match (true) {
                !file_exists($filepath) => "\n No backup file was created",
                filesize($filepath) === 0 => "\n Backup file is empty",
                default => ''
            };
            echo $msg . PHP_EOL;
            $this->emailError($msg);
            $this->sendErrorAlert($msg);
        }
    }

    private function emailDatabaseBackup($filepath): void
    {
        EmailQueue::enqueue(
            Configure::read('supportEmail'),
            ['content' => 'Database backup attached'],
            [
                'subject' => 'Vore Arts Fund database backup (' . getEnvironment() . ')',
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
                'subject' => 'Vore Arts Fund database backup failed (' . getEnvironment() . ')',
                'template' => 'default',
                'from_name' => 'Vore Arts Fund',
                'from_email' => 'noreply@voreartsfund.org',
            ],
        );
    }

    private function uploadFileToS3($filepath)
    {
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-2',
            'credentials' => [
                'key'    => Configure::read('s3.accessKey'),
                'secret' => Configure::read('s3.secretAccessKey'),
            ],
        ]);

        $filename = basename($filepath);
        try {
            $result = $s3->putObject([
                'Bucket' => Configure::read('s3.buckets.dbBackups'),
                'Key'    => $filename,
                'SourceFile' => $filepath,
            ]);

            echo 'Database backup uploaded to S3 successfully: ' . $result['ObjectURL'] . PHP_EOL;
            $this->sendSuccessAlert($result['ObjectURL']);
        } catch (\Aws\Exception\AwsException $e) {
            $msg = 'Error uploading database backup to S3: ' . $e->getMessage() . PHP_EOL;
            echo $msg;
            $this->sendErrorAlert($msg);
        }
    }

    private function sendErrorAlert(string $msg): void
    {
        $alert = new Alert();
        $alert->addLine($msg);
        $alert->send(Alert::TYPE_ERRORS);
    }

    private function sendSuccessAlert($url): void
    {
        $alert = new Alert();
        $alert->addLine('Database backup created successfully: ' . $url);
        $alert->send(Alert::TYPE_CRONS);
    }
}
