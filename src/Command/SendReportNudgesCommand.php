<?php
declare(strict_types=1);

namespace App\Command;

use App\Alert\Alert;
use App\Alert\ErrorAlert;
use App\Email\MailConfig;
use App\Model\Entity\Nudge;
use App\Model\Entity\Project;
use App\Model\Table\NudgesTable;
use App\Model\Table\UsersTable;
use Aws\S3\S3Client;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\I18n\FrozenDate;
use Cake\Routing\Router;
use EmailQueue\EmailQueue;

/**
 * DatabaseBackup command.
 */
class SendReportNudgesCommand extends Command
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
        // Get all projects that have received a loan and have not submitted a finalized report
        $projectsTable = $this->getTableLocator()->get('Projects');

        /** @var Project[] $projects */
        $threshold = '-1 month'; // Will wait this long after loan awarded date and between nudges
        $projects = $projectsTable
            ->find('notDeleted')
            ->find('loadRecipients')
            ->find('notFinalized')
            ->find('withoutRecentNudge', [
                'nudgeType' => Nudge::TYPE_REPORT_REMINDER,
                'threshold' => $threshold,
            ])
            ->where(['Project.loan_awarded_date <' => new FrozenDate($threshold)])
            ->all();

        $io->out(sprintf(
            'Found %s %s that need report nudges',
            $projects->count(),
            __n('report', 'reports', $projects->count())
        ));

        if ($projects->isEmpty()) {
            $io->out('Done');
            return;
        }

        /** @var UsersTable $usersTable */
        $usersTable = $this->getTableLocator()->get('Users');

        /** @var NudgesTable $nudgesTable */
        $nudgesTable = $this->getTableLocator()->get('Nudges');

        $mailConfig = new MailConfig();

        foreach ($projects as $project) {
            try {
                $user = $usersTable->get($project->user_id);
                EmailQueue::enqueue(
                    $user->email,
                    [
                        'projectTitle' => $project->title,
                        'userName' => $user->name,
                        'reportUrl' => Router::url(
                            [
                                'prefix' => false,
                                'plugin' => false,
                                'controller' => 'Reports',
                                'action' => 'submit',
                                $project->id,
                            ],
                            true
                        ),
                        'supportEmail' => Configure::read('supportEmail'),
                    ],
                    [
                        'subject' => $mailConfig->subjectPrefix . 'Report Reminder',
                        'template' => 'nudges/report_reminder',
                        'from_name' => $mailConfig->fromName,
                        'from_email' => $mailConfig->fromEmail,
                    ]
                );
            } catch (\Exception $e) {
                $msg = "- Error processing report nudge for project #{$project->id}: " . $e->getMessage();
                $io->error($msg);
                (new ErrorAlert())->send($msg);
                continue;
            }
            $nudgesTable->addNudge($user->id, $project->id, Nudge::TYPE_REPORT_REMINDER);
            $io->success("- Sent report nudge for project #{$project->id}");
        }

        $io->out('Done');
    }
}
