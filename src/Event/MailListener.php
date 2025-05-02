<?php

namespace App\Event;

use App\Model\Entity\Project;
use App\Model\Entity\User;
use App\Model\Table\FundingCyclesTable;
use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Http\Exception\InternalErrorException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use EmailQueue\EmailQueue;

class MailListener implements EventListenerInterface
{
    private FundingCyclesTable $fundingCyclesTable;
    private UsersTable $usersTable;
    private string $fromEmail;
    private string $fromName = 'Vore Arts Fund';
    public static string $subjectPrefix = 'Vore Arts Fund - ';

    public function __construct()
    {
        $this->fromEmail = Configure::read('noReplyEmail');
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
            'Project.revisionRequested' => 'mailProjectRevisionRequested',
            'Project.rejected' => 'mailProjectRejected',
            'Project.funded' => 'mailProjectFunded',
            'Project.notFunded' => 'mailProjectNotFunded',
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
        list($email, $name) = $this->getRecipientFromProject($project);
        $this->enqueueEmail(
            $email,
            [
                'project' => $project,
                'fundingCycle' => $this->fundingCyclesTable->get($project->funding_cycle_id),
                'userName' => $name,
            ],
            [
                'subject' => self::$subjectPrefix . 'Application Accepted',
                'template' => 'application_accepted',
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
            ]
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
        list($email, $name) = $this->getRecipientFromProject($project);
        $this->enqueueEmail(
            $email,
            [
                'project' => $project,
                'fundingCycle' => $this->fundingCyclesTable->get($project->funding_cycle_id),
                'note' => $note,
                'url' => Router::url([
                    'prefix' => 'My',
                    'controller' => 'Projects',
                    'action' => 'edit',
                    'id' => $project->id,
                ], true),
                'userName' => $name,
            ],
            [
                'subject' => self::$subjectPrefix . 'Revision Requested',
                'template' => 'application_revision_requested',
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
            ],
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
        list($email, $name) = $this->getRecipientFromProject($project);
        $this->enqueueEmail(
            $email,
            [
                'project' => $project,
                'fundingCycle' => $this->fundingCyclesTable->find('nextApplying')->first(),
                'note' => $note,
                'userName' => $name,
            ],
            [
                'subject' => self::$subjectPrefix . 'Application Not Accepted',
                'template' => 'application_rejected',
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
            ],
        );
    }

    /**
     * @param Event $event
     * @param Project $project
     * @param int $amount
     * @return void
     * @throws InternalErrorException
     */
    public function mailProjectFunded(Event $event, Project $project): void
    {
        list($email, $name) = $this->getRecipientFromProject($project);
        $this->enqueueEmail(
            $email,
            [
                'project' => $project,
                'fundingCycle' => $this->fundingCyclesTable->get($project->funding_cycle_id),
                'loanAgreementUrl' => Router::url([
                    'prefix' => 'My',
                    'controller' => 'Projects',
                    'action' => 'loanAgreement',
                    'id' => $project->id,
                ], true),
                'userName' => $name,
                'replyUrl' => $this->getReplyUrl($project),
            ],
            [
                'subject' => self::$subjectPrefix . 'Application Funded',
                'template' => 'application_funded',
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
            ]
        );
    }

    /**
     * Enqueues message and dispatches event
     *
     * @param string $email Recipient email address
     * @param array $viewVars View vars to pass to email template
     * @param array $emailOptions
     * @param string $event Event name to dispatch
     * @return void
     */
    private function enqueueEmail($email, $viewVars, $emailOptions, $event = 'Mail.messageSentToApplicant'): void
    {
        EmailQueue::enqueue(
            $email,
            $viewVars,
            $emailOptions
        );
        EventManager::instance()->on(new AlertListener());
        EventManager::instance()->dispatch(new Event(
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
        list($email, $name) = $this->getRecipientFromProject($project);
        $this->enqueueEmail(
            $email,
            [
                'project' => $project,
                'fundingCycle' => $this->fundingCyclesTable->find('nextApplying')->first(),
                'userName' => $name,
            ],
            [
                'subject' => self::$subjectPrefix . 'Application Not Funded',
                'template' => 'application_not_funded',
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
            ],
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
        list($email, $name) = $this->getRecipientFromProject($project);
        $this->enqueueEmail(
            $email,
            [
                'project' => $project,
                'userName' => $name,
                'message' => $message,
                'replyUrl' => $this->getReplyUrl($project),
            ],
            [
                'subject' => self::$subjectPrefix . 'Message from review committee',
                'template' => 'message',
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
            ],
        );
    }

    private function getReplyUrl(Project $project): string
    {
        return Router::url(
            [
                'prefix' => 'My',
                'controller' => 'Projects',
                'action' => 'messages',
                'id' => $project->id
            ],
            true
        );
    }

    public function mailFundingDisbursed(Project $project): void
    {
        list($email, $name) = $this->getRecipientFromProject($project);
        $this->enqueueEmail(
            $email,
            [
                'project' => $project,
                'myProjectsUrl' => Router::url([
                    'prefix' => 'My',
                    'controller' => 'Projects',
                    'action' => 'index'
                ], true),
                'userName' => $name,
                'replyUrl' => $this->getReplyUrl($project),
            ],
            [
                'subject' => self::$subjectPrefix . 'Your check is on its way',
                'template' => 'funding_disbursed',
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
            ],
        );
    }
}
