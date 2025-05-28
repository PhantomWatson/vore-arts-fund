<?php

namespace App\Nudges;

use App\Alert\ErrorAlert;
use App\Email\MailConfig;
use App\Model\Entity\Nudge;
use App\Model\Entity\Project;
use App\Model\Table\NudgesTable;
use Cake\Core\Configure;
use Cake\Datasource\ResultSetInterface;
use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use EmailQueue\EmailQueue;

class ReportReminderNudge implements NudgeInterface
{
    /**
     * Projects that have received a loan and have not submitted a finalized report
     *
     * @return ResultSetInterface|Project[]
     */
    public static function getProjects(): ResultSetInterface
    {
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');

        /** @var Project[] $projects */
        $threshold = '-1 month'; // Will wait this long after loan awarded date and between nudges
        return $projectsTable
            ->find('notDeleted')
            ->find('loadRecipients')
            ->find('notFinalized')
            ->find('withoutRecentNudge', [
                'nudgeType' => Nudge::TYPE_REPORT_REMINDER,
                'threshold' => $threshold,
            ])
            ->where(['Projects.loan_awarded_date <' => new FrozenDate($threshold)])
            ->all();
    }

    public static function send(Project $project): bool|string
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $mailConfig = new MailConfig();

        try {
            $user = $usersTable->get($project->user_id);
            $viewVars = [
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
            ];
            $mailOptions = [
                'subject' => $mailConfig->subjectPrefix . 'Report Reminder',
                'template' => 'nudges/report_reminder',
                'from_name' => $mailConfig->fromName,
                'from_email' => $mailConfig->fromEmail,
            ];
            EmailQueue::enqueue($user->email, $viewVars, $mailOptions);
        } catch (\Exception $e) {
            $msg = "Error processing report nudge for project #{$project->id}: " . $e->getMessage();
            (new ErrorAlert())->send($msg);
            return $msg;
        }

        /** @var NudgesTable $nudgesTable */
        $nudgesTable = TableRegistry::getTableLocator()->get('Nudges');
        $nudgesTable->addNudge($user->id, $project->id, Nudge::TYPE_REPORT_REMINDER);
        return true;
    }
}
