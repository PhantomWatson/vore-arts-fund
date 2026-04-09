<?php

namespace App\Mailer;

use App\Email\MailConfig;
use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class ProjectMailer extends Mailer
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

    private function getReplyUrl(int $projectId): string
    {
        return Router::url([
            'prefix' => 'My',
            'controller' => 'Projects',
            'action' => 'messages',
            'id' => $projectId,
        ], true);
    }

    public function accepted(int $projectId): void
    {
        [$project, $user] = $this->fetchProjectAndUser($projectId);
        $fundingCycle = TableRegistry::getTableLocator()->get('FundingCycles')->get($project->funding_cycle_id);
        $this->setupEmail(
            'Application Accepted',
            'application_accepted',
            $user->email,
            [
                'fundingCycle' => $fundingCycle,
                'project' => $project,
                'userName' => $user->name,
            ]
        );
    }

    public function fundingDisbursed(int $projectId): void
    {
        [$project, $user] = $this->fetchProjectAndUser($projectId);
        $this->setupEmail(
            'Your check is on its way',
            'funding_disbursed',
            $user->email,
            [
                'myReportsUrl' => Router::url([
                    'prefix' => 'My',
                    'controller' => 'Reports',
                    'action' => 'index',
                ], true),
                'project' => $project,
                'replyUrl' => $this->getReplyUrl($projectId),
                'userName' => $user->name,
            ]
        );
    }

    public function funded(int $projectId): void
    {
        [$project, $user] = $this->fetchProjectAndUser($projectId);
        $fundingCycle = TableRegistry::getTableLocator()->get('FundingCycles')->get($project->funding_cycle_id);
        $this->setupEmail(
            'Application Funded',
            'application_funded',
            $user->email,
            [
                'fundingCycle' => $fundingCycle,
                'loanAgreementUrl' => Router::url([
                    'prefix' => 'My',
                    'controller' => 'Loans',
                    'action' => 'loanAgreement',
                    'id' => $projectId,
                ], true),
                'project' => $project,
                'replyUrl' => $this->getReplyUrl($projectId),
                'userName' => $user->name,
            ]
        );
    }

    public function message(int $projectId, string $message): void
    {
        [$project, $user] = $this->fetchProjectAndUser($projectId);
        $this->setupEmail(
            'Message from review committee',
            'message',
            $user->email,
            [
                'message' => $message,
                'project' => $project,
                'replyUrl' => $this->getReplyUrl($projectId),
                'userName' => $user->name,
            ]
        );
    }

    public function notFunded(int $projectId): void
    {
        [$project, $user] = $this->fetchProjectAndUser($projectId);
        $currentApplyingFundingCycle = TableRegistry::getTableLocator()->get('FundingCycles')->find('currentApplying')->first();
        $this->setupEmail(
            'Application Not Funded',
            'application_not_funded',
            $user->email,
            [
                'currentApplyingFundingCycle' => $currentApplyingFundingCycle,
                'project' => $project,
                'reapplyUrl' => Router::url([
                    'controller' => 'Projects',
                    'action' => 'apply',
                    '?' => ['reapply' => $projectId],
                ], true),
                'userName' => $user->name,
            ]
        );
    }

    public function rejected(int $projectId, string $note): void
    {
        [$project, $user] = $this->fetchProjectAndUser($projectId);
        $fundingCycle = TableRegistry::getTableLocator()->get('FundingCycles')->find('nextApplying')->first();
        $this->setupEmail(
            'Application Not Accepted',
            'application_rejected',
            $user->email,
            [
                'fundingCycle' => $fundingCycle,
                'note' => $note,
                'project' => $project,
                'userName' => $user->name,
            ]
        );
    }

    public function revisionRequested(int $projectId, string $note): void
    {
        [$project, $user] = $this->fetchProjectAndUser($projectId);
        $fundingCycle = TableRegistry::getTableLocator()->get('FundingCycles')->get($project->funding_cycle_id);
        $this->setupEmail(
            'Revision Requested',
            'application_revision_requested',
            $user->email,
            [
                'fundingCycle' => $fundingCycle,
                'note' => $note,
                'project' => $project,
                'url' => Router::url([
                    'prefix' => 'My',
                    'controller' => 'Projects',
                    'action' => 'edit',
                    'id' => $projectId,
                ], true),
                'userName' => $user->name,
            ]
        );
    }
}
