<?php

namespace App\Mailer;

use App\Email\MailConfig;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class NudgeMailer extends Mailer
{
    private function setupEmail(string $subject, string $template, string $toEmail, array $viewVars): void
    {
        $mailConfig = new MailConfig();
        $this
            ->setFrom([$mailConfig->fromEmail => $mailConfig->fromName])
            ->setSubject($mailConfig->subjectPrefix . $subject)
            ->setTo($toEmail)
            ->setViewVars($viewVars);
        $this->viewBuilder()->setTemplate($template);
    }

    private function fetchProjectAndUser(int $projectId): array
    {
        $project = TableRegistry::getTableLocator()->get('Projects')->get($projectId);
        $user = TableRegistry::getTableLocator()->get('Users')->get($project->user_id);
        return [$project, $user];
    }

    public function paymentReminder(int $projectId): void
    {
        [$project, $user] = $this->fetchProjectAndUser($projectId);
        $balance = $project->getBalance();
        $this->setupEmail(
            'Payment Reminder',
            'nudges/payment_reminder',
            $user->email,
            [
                'balance' => '$' . number_format($balance / 100, 2),
                'projectTitle' => $project->title,
                'repaymentUrl' => Router::url([
                    'prefix' => 'My',
                    'plugin' => false,
                    'controller' => 'Loans',
                    'action' => 'payment',
                    'id' => $projectId,
                ], true),
                'supportEmail' => Configure::read('supportEmail'),
                'userName' => $user->name,
            ]
        );
    }

    public function reportDue(int $projectId): void
    {
        [$project, $user] = $this->fetchProjectAndUser($projectId);
        $this->setupEmail(
            'Report Due',
            'nudges/report_due',
            $user->email,
            [
                'deadline' => $project->loan_awarded_date->addYears(1)->format('F j, Y'),
                'projectTitle' => $project->title,
                'repaymentUrl' => $project->is_repaid
                    ? null
                    : Router::url([
                        'prefix' => 'My',
                        'plugin' => false,
                        'controller' => 'Loans',
                        'action' => 'payment',
                        'id' => $projectId,
                    ], true),
                'reportUrl' => Router::url([
                    'prefix' => 'My',
                    'plugin' => false,
                    'controller' => 'Reports',
                    'action' => 'submit',
                    'id' => $projectId,
                ], true),
                'supportEmail' => Configure::read('supportEmail'),
                'userName' => $user->name,
            ]
        );
    }

    public function reportReminder(int $projectId): void
    {
        [$project, $user] = $this->fetchProjectAndUser($projectId);
        $this->setupEmail(
            'Report Reminder',
            'nudges/report_reminder',
            $user->email,
            [
                'projectTitle' => $project->title,
                'repaymentUrl' => $project->is_repaid
                    ? null
                    : Router::url([
                        'prefix' => 'My',
                        'plugin' => false,
                        'controller' => 'Loans',
                        'action' => 'payment',
                        'id' => $projectId,
                    ], true),
                'reportUrl' => Router::url([
                    'prefix' => 'My',
                    'plugin' => false,
                    'controller' => 'Reports',
                    'action' => 'submit',
                    'id' => $projectId,
                ], true),
                'supportEmail' => Configure::read('supportEmail'),
                'userName' => $user->name,
            ]
        );
    }

    public function voteReminder(int $projectId): void
    {
        $project = TableRegistry::getTableLocator()->get('Projects')->get($projectId);
        $user = TableRegistry::getTableLocator()->get('Users')->get($project->user_id);
        $fundingCycle = TableRegistry::getTableLocator()->get('FundingCycles')->get($project->funding_cycle_id);
        $this->setupEmail(
            'Voting has begun!',
            'nudges/vote_reminder',
            $user->email,
            [
                'deadline' => $fundingCycle->vote_end_local->format('F jS'),
                'projectTitle' => $project->title,
                'userName' => $user->name,
            ]
        );
    }
}
