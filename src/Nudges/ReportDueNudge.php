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

class ReportDueNudge implements NudgeInterface
{
    /**
     * Non-finalized projects that have received a loan more than 11 months ago and haven't received a nudge in the
     * last week or submitted a report in the last 11 months
     *
     * @return ResultSetInterface|Project[]
     */
    public static function getProjects(): ResultSetInterface
    {
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');

        $nudgeThreshold = '-1 week';
        $sinceLoanAwardedThreshold = '-11 months';
        $sinceLastReportThreshold = '-11 months';

        /** @var Project[] $projects */
        return $projectsTable
            ->find('loanRecipients')
            ->find('notDeleted')
            ->find('notFinalized')
            ->find('withoutRecentNudge', [
                'nudgeType' => [Nudge::TYPE_REPORT_DUE],
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
                'deadline' => $project->loan_awarded_date->addYear(1)->format('F j, Y'),
            ];
            $mailOptions = [
                'subject' => $mailConfig->subjectPrefix . 'Report Due',
                'template' => 'nudges/report_due',
                'from_name' => $mailConfig->fromName,
                'from_email' => $mailConfig->fromEmail,
            ];
            EmailQueue::enqueue($user->email, $viewVars, $mailOptions);
            AlertEmitter::emitMessageSentEvent($user->email, $mailOptions['subject'], $viewVars, $mailOptions['template']);
        } catch (\Exception $e) {
            $msg = "Error processing report due nudge for project #{$project->id}: " . $e->getMessage();
            (new ErrorAlert())->send($msg);
            return $msg;
        }

        /** @var NudgesTable $nudgesTable */
        $nudgesTable = TableRegistry::getTableLocator()->get('Nudges');
        $nudgesTable->addNudge($user->id, $project->id, Nudge::TYPE_REPORT_DUE);
        return true;
    }
}
