<?php

namespace App\Nudges;

use App\Alert\ErrorAlert;
use App\Email\MailConfig;
use App\Event\AlertEmitter;
use App\Event\AlertListener;
use App\Model\Entity\Nudge;
use App\Model\Entity\Project;
use App\Model\Entity\Transaction;
use App\Model\Table\NudgesTable;
use Cake\Core\Configure;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use EmailQueue\EmailQueue;

class PaymentNudge implements NudgeInterface
{
    /**
     * Projects with loans with positive balances
     *
     * @return ResultSetInterface|Project[]
     */
    public static function getProjects(): ResultSetInterface
    {
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');

        $delayBeforeNudges = '-6 months';
        $delayAfterRepayment = '-1 month';
        $delayBetweenNudges = '-1 month';
        return $projectsTable
            ->find('notDeleted')
            ->find('withOutstandingLoan')

            // Not recently-awarded loans
            ->where(['Projects.loan_awarded_date <' => new FrozenDate($delayBeforeNudges)])

            // Not recently-nudged
            ->find('withoutRecentNudge', [
                'nudgeType' => [Nudge::TYPE_PAYMENT_REMINDER],
                'threshold' => $delayBetweenNudges,
            ])

            // No recent repayments
            ->notMatching('Transactions', function ($q) use ($delayAfterRepayment) {
                return $q->where([
                    'Transactions.created >' => new FrozenDate($delayAfterRepayment),
                    'Transactions.type' => Transaction::TYPE_LOAN_REPAYMENT,
                ]);
            })
            ->all();
    }

    public static function send(Project $project): bool|string
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $mailConfig = new MailConfig();

        $balance = $project->getBalance();
        if ($balance <= 0) {
            $errorMsg = "Can\'t send payment reminder for project #{$project->id}; project has no outstanding balance.";
            (new ErrorAlert())->send($errorMsg);
            return $errorMsg;
        }

        try {
            $user = $usersTable->get($project->user_id);
            $viewVars = [
                'projectTitle' => $project->title,
                'userName' => $user->name,
                'repaymentUrl' => Router::url(
                    [
                        'prefix' => 'My',
                        'plugin' => false,
                        'controller' => 'Loans',
                        'action' => 'payment',
                        'id' => $project->id,
                    ],
                    true
                ),
                'supportEmail' => Configure::read('supportEmail'),
                'balance' => '$' . number_format($balance / 100, 2),
            ];
            $mailOptions = [
                'subject' => $mailConfig->subjectPrefix . 'Payment Reminder',
                'template' => 'nudges/payment_reminder',
                'from_name' => $mailConfig->fromName,
                'from_email' => $mailConfig->fromEmail,
            ];
            EmailQueue::enqueue($user->email, $viewVars, $mailOptions);
            AlertEmitter::emitMessageSentEvent($user->email, $mailOptions['subject'], $viewVars, $mailOptions['template']);
        } catch (\Exception $e) {
            $msg = "Error processing payment nudge for project #{$project->id}: " . $e->getMessage();
            (new ErrorAlert())->send($msg);
            return $msg;
        }

        /** @var NudgesTable $nudgesTable */
        $nudgesTable = TableRegistry::getTableLocator()->get('Nudges');
        $nudgesTable->addNudge($user->id, $project->id, Nudge::TYPE_PAYMENT_REMINDER);
        return true;
    }
}
