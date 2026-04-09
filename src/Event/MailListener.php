<?php

namespace App\Event;

use App\Email\MailConfig;
use App\Mailer\ProjectMailer;
use App\Model\Entity\Project;
use App\Model\Entity\User;
use App\Model\Table\FundingCyclesTable;
use App\Model\Table\UsersTable;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Http\Exception\InternalErrorException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Queue\Model\Table\QueuedJobsTable;

class MailListener implements EventListenerInterface
{
    private FundingCyclesTable $fundingCyclesTable;
    private UsersTable $usersTable;
    public MailConfig $mailConfig;

    public function __construct()
    {
        $this->mailConfig = new MailConfig();
        $this->usersTable = TableRegistry::getTableLocator()->get('Users');
        $this->fundingCyclesTable = TableRegistry::getTableLocator()->get('FundingCycles');
    }

    /**
     * @param Project $project
     * @return array|false
     */
    private function getRecipientFromProject(Project $project): bool|array
    {
        try {
            /** @var User $user */
            $user = $this->usersTable->get($project->user_id ?? false);
            return [$user->email, $user->name];
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            Log::error("Project #{$project->id} does not have a valid user ID (#$project->user_id)");
            return false;
        }
    }

    public function implementedEvents(): array
    {
        return [
            'Project.accepted' => 'mailProjectAccepted',
            'Project.funded' => 'mailProjectFunded',
            'Project.notFunded' => 'mailProjectNotFunded',
            'Project.rejected' => 'mailProjectRejected',
            'Project.revisionRequested' => 'mailProjectRevisionRequested',
            'Note.messageSent' => 'mailMessage',
        ];
    }

    /**
     * @param Event $event
     * @param Project $project
     * @return void
     * @throws InternalErrorException
     */
    public function mailProjectAccepted(Event $event, Project $project): void
    {
        [$email, $name] = $this->getRecipientFromProject($project);
        $fundingCycle = $this->fundingCyclesTable->get($project->funding_cycle_id);
        $this->enqueueEmailAndDispatchEvent(
            $email,
            [
                'fundingCycle' => $fundingCycle,
                'project' => $project,
                'userName' => $name,
            ],
            [
                'subject' => $this->mailConfig->subjectPrefix . 'Application Accepted',
                'template' => 'application_accepted',
            ],
            ProjectMailer::class,
            'accepted',
            [$project->id],
        );
    }

    /**
     * @param Event $event
     * @param Project $project
     * @param string $note
     * @return void
     * @throws InternalErrorException
     */
    public function mailProjectRevisionRequested(Event $event, Project $project, string $note): void
    {
        [$email, $name] = $this->getRecipientFromProject($project);
        $fundingCycle = $this->fundingCyclesTable->get($project->funding_cycle_id);
        $this->enqueueEmailAndDispatchEvent(
            $email,
            [
                'fundingCycle' => $fundingCycle,
                'note' => $note,
                'project' => $project,
                'url' => Router::url([
                    'prefix' => 'My',
                    'controller' => 'Projects',
                    'action' => 'edit',
                    'id' => $project->id,
                ], true),
                'userName' => $name,
            ],
            [
                'subject' => $this->mailConfig->subjectPrefix . 'Revision Requested',
                'template' => 'application_revision_requested',
            ],
            ProjectMailer::class,
            'revisionRequested',
            [$project->id, $note],
        );
    }

    /**
     * @param Event $event
     * @param Project $project
     * @param string $note
     * @return void
     * @throws InternalErrorException
     */
    public function mailProjectRejected(Event $event, Project $project, string $note): void
    {
        [$email, $name] = $this->getRecipientFromProject($project);
        $this->enqueueEmailAndDispatchEvent(
            $email,
            [
                'fundingCycle' => $this->fundingCyclesTable->find('nextApplying')->first(),
                'note' => $note,
                'project' => $project,
                'userName' => $name,
            ],
            [
                'subject' => $this->mailConfig->subjectPrefix . 'Application Not Accepted',
                'template' => 'application_rejected',
            ],
            ProjectMailer::class,
            'rejected',
            [$project->id, $note],
        );
    }

    /**
     * @param Event $event
     * @param Project $project
     * @return void
     * @throws InternalErrorException
     */
    public function mailProjectFunded(Event $event, Project $project): void
    {
        [$email, $name] = $this->getRecipientFromProject($project);
        $fundingCycle = $this->fundingCyclesTable->get($project->funding_cycle_id);
        $this->enqueueEmailAndDispatchEvent(
            $email,
            [
                'fundingCycle' => $fundingCycle,
                'loanAgreementUrl' => Router::url([
                    'prefix' => 'My',
                    'controller' => 'Loans',
                    'action' => 'loanAgreement',
                    'id' => $project->id,
                ], true),
                'project' => $project,
                'replyUrl' => $this->getReplyUrl($project),
                'userName' => $name,
            ],
            [
                'subject' => $this->mailConfig->subjectPrefix . 'Application Funded',
                'template' => 'application_funded',
            ],
            ProjectMailer::class,
            'funded',
            [$project->id],
        );
    }

    /**
     * Enqueues a mailer job and dispatches an alert event
     *
     * @param string $email Recipient email address (used for alerting only)
     * @param array $viewVars View vars to pass to the alert template renderer
     * @param array $emailOptions Must include 'subject' and 'template' keys (used for alerting only)
     * @param string $mailerClass Fully-qualified Mailer class name
     * @param string $mailerAction Method name on the Mailer class
     * @param array $mailerVars Arguments to pass to the Mailer method
     * @param string $event Event name to dispatch
     * @return void
     */
    private function enqueueEmailAndDispatchEvent(
        string $email,
        array $viewVars,
        array $emailOptions,
        string $mailerClass,
        string $mailerAction,
        array $mailerVars,
        string $event = 'Mail.messageSentToApplicant'
    ): void {
        /** @var QueuedJobsTable $jobsTable */
        $jobsTable = TableRegistry::getTableLocator()->get('Queue.QueuedJobs');
        $jobsTable->createJob('Queue.Mailer', [
            'action' => $mailerAction,
            'class' => $mailerClass,
            'vars' => $mailerVars,
        ]);

        $eventManager = EventManager::instance();

        // Only register the listener if it hasn't been registered yet
        if (!AlertListener::hasAlertListener($eventManager, $event)) {
            $eventManager->on(new AlertListener());
        }

        $eventManager->dispatch(new Event(
            $event,
            $this,
            [
                'email' => $email,
                'subject' => $emailOptions['subject'],
                'viewVars' => $viewVars,
                'template' => $emailOptions['template'],
            ]
        ));
    }

    /**
     * @param Event $event
     * @param Project $project
     * @return void
     * @throws InternalErrorException
     */
    public function mailProjectNotFunded(Event $event, Project $project): void
    {
        [$email, $name] = $this->getRecipientFromProject($project);

        $this->enqueueEmailAndDispatchEvent(
            $email,
            [
                'currentApplyingFundingCycle' => $this->fundingCyclesTable->find('currentApplying')->first(),
                'project' => $project,
                'reapplyUrl' => Router::url([
                    'controller' => 'Projects',
                    'action' => 'apply',
                    '?' => ['reapply' => $project->id],
                ], true),
                'userName' => $name,
            ],
            [
                'subject' => $this->mailConfig->subjectPrefix . 'Application Not Funded',
                'template' => 'application_not_funded',
            ],
            ProjectMailer::class,
            'notFunded',
            [$project->id],
        );
    }

    /**
     * Mails a message composed by the review committee regarding a project
     *
     * I mean, everything that's mailed is "a message", but this method specifically correlates to the Message model.
     *
     * @param Event $event
     * @param Project $project
     * @param string $message
     * @return void
     * @throws InternalErrorException
     */
    public function mailMessage(Event $event, Project $project, string $message): void
    {
        /** @var QueuedJobsTable $jobsTable */
        $jobsTable = TableRegistry::getTableLocator()->get('Queue.QueuedJobs');
        $jobsTable->createJob('Queue.Mailer', [
            'action' => 'message',
            'class' => ProjectMailer::class,
            'vars' => [$project->id, $message],
        ]);
    }

    private function getReplyUrl(Project $project): string
    {
        return Router::url(
            [
                'prefix' => 'My',
                'controller' => 'Projects',
                'action' => 'messages',
                'id' => $project->id,
            ],
            true
        );
    }

    public function mailFundingDisbursed(Project $project): void
    {
        [$email, $name] = $this->getRecipientFromProject($project);
        $this->enqueueEmailAndDispatchEvent(
            $email,
            [
                'myReportsUrl' => Router::url([
                    'prefix' => 'My',
                    'controller' => 'Reports',
                    'action' => 'index',
                ], true),
                'project' => $project,
                'replyUrl' => $this->getReplyUrl($project),
                'userName' => $name,
            ],
            [
                'subject' => $this->mailConfig->subjectPrefix . 'Your check is on its way',
                'template' => 'funding_disbursed',
            ],
            ProjectMailer::class,
            'fundingDisbursed',
            [$project->id],
        );
    }
}
