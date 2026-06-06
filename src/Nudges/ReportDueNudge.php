<?php

namespace App\Nudges;

use App\Alert\ErrorAlert;
use App\Email\MailConfig;
use App\Event\AlertEmitter;
use App\Model\Entity\Nudge;
use App\Model\Entity\Project;
use App\Model\Table\NudgesTable;
use Cake\Core\Configure;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use App\Mailer\NudgeMailer;
use Queue\Model\Table\QueuedJobsTable;

class ReportDueNudge implements NudgeInterface
{
    /**
     * Non-repaid projects that have received a loan more than 11 months ago and haven't received a nudge in the
     * last week or submitted a report in the last 11 months
     *
     * @return ResultSet<Project>|null
     */
    public static function getProjects(): ?ResultSetInterface
    {
        return TableRegistry::getTableLocator()
            ->get('Projects')
            ->find('withOutstandingLoan')
            ->find(
                'withoutRecentNudge',
                nudgeType: [Nudge::TYPE_REPORT_DUE],
                threshold: '-1 week',
            )
            ->find('withReportAlmostDue')
            ->all();
    }

    public static function send(Project $project): bool|string
    {
        $reportsTable = TableRegistry::getTableLocator()->get('Reports');
        $deadline = $reportsTable->getDeadlineForNextReport($project);
        $now = new \DateTimeImmutable();
        if ($deadline < $now) {
            // If the deadline has already passed, we shouldn't be sending a nudge about it being due
            $msg = "The system attempted to send a \"report due\" nudge for project #$project->id despite that deadline having already passed";
            new ErrorAlert()->send($msg);
            return $msg;
        }

        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $mailConfig = new MailConfig();

        try {
            $user = $usersTable->get($project->user_id);
            $viewVars = [
                'projectTitle' => $project->title,
                'userName' => $user->name,
                'reportUrl' => Router::url(
                    [
                        'prefix' => 'My',
                        'plugin' => false,
                        'controller' => 'Reports',
                        'action' => 'submit',
                        'id' => $project->id,
                    ],
                    true
                ),
                'supportEmail' => Configure::read('supportEmail'),
                'repaymentUrl' => $project->is_repaid
                    ? null
                    : Router::url(
                        [
                            'prefix' => 'My',
                            'plugin' => false,
                            'controller' => 'Loans',
                            'action' => 'payment',
                            'id' => $project->id,
                        ],
                        true
                    ),
                'deadline' => $deadline->format('F j, Y'),
            ];
            $mailOptions = [
                'subject' => $mailConfig->subjectPrefix . 'Report Due',
                'template' => 'nudges/report_due',
            ];
            /** @var QueuedJobsTable $jobsTable */
            $jobsTable = TableRegistry::getTableLocator()->get('Queue.QueuedJobs');
            $jobsTable->createJob('Queue.Mailer', [
                'action' => 'reportDue',
                'class' => NudgeMailer::class,
                'vars' => [$project->id],
            ]);
            AlertEmitter::emitMessageSentEvent($user->email, $mailOptions['subject'], $viewVars, $mailOptions['template']);
        } catch (\Exception $e) {
            $msg = "Error processing report due nudge for project #{$project->id}: " . $e->getMessage();
            new ErrorAlert()->send($msg);
            return $msg;
        }

        /** @var NudgesTable $nudgesTable */
        $nudgesTable = TableRegistry::getTableLocator()->get('Nudges');
        $nudgesTable->addNudge($user->id, $project->id, Nudge::TYPE_REPORT_DUE);
        return true;
    }
}
