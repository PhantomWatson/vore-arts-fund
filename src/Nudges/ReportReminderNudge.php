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
use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use EmailQueue\EmailQueue;

class ReportReminderNudge implements NudgeInterface
{
    /**
     * Non-finalized projects that have received a loan more than two months ago and haven't received a nudge in the
     * last month or submitted a report in the last six months
     *
     * @return ResultSetInterface|Project[]
     */
    public static function getProjects(): ResultSetInterface
    {
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');

        $nudgeThreshold = '-1 month';
        $sinceLoanAwardedThreshold = '-2 months';
        $sinceLastReportThreshold = '-6 months';

        /** @var Project[] $projects */
        return $projectsTable
            ->find('loanRecipients')
            ->find('notDeleted')
            ->find('notFinalized')
            ->find('withoutRecentNudge', [
                'nudgeType' => [Nudge::TYPE_REPORT_REMINDER, Nudge::TYPE_REPORT_DUE],
                'threshold' => $nudgeThreshold,
            ])
            ->find('withoutRecentReports', ['threshold' => $sinceLastReportThreshold])
            ->where([
                'Projects.loan_awarded_date IS NOT' => null,
                'Projects.loan_awarded_date <' => new FrozenDate($sinceLoanAwardedThreshold)
            ])
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
            ];
            $mailOptions = [
                'subject' => $mailConfig->subjectPrefix . 'Report Reminder',
                'template' => 'nudges/report_reminder',
                'from_name' => $mailConfig->fromName,
                'from_email' => $mailConfig->fromEmail,
            ];
            EmailQueue::enqueue($user->email, $viewVars, $mailOptions);
            AlertEmitter::emitMessageSentEvent($user->email, $mailOptions['subject'], $viewVars, $mailOptions['template']);
        } catch (\Exception $e) {
            $msg = "Error processing report reminder nudge for project #{$project->id}: " . $e->getMessage();
            (new ErrorAlert())->send($msg);
            return $msg;
        }

        /** @var NudgesTable $nudgesTable */
        $nudgesTable = TableRegistry::getTableLocator()->get('Nudges');
        $nudgesTable->addNudge($user->id, $project->id, Nudge::TYPE_REPORT_REMINDER);
        return true;
    }
}
