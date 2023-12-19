<?php

namespace App\Event;

use App\Model\Entity\Project;
use App\Model\Entity\User;
use App\Model\Table\FundingCyclesTable;
use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Http\Exception\InternalErrorException;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
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
    private function getRecipientFromProject(Project $project)
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
        ];
    }

    /**
     * @param Event $event
     * @param Project $project
     * @return void
     * @throws InternalErrorException
     */
    public function mailProjectAccepted(Event $event, Project $project)
    {
        list($email, $name) = $this->getRecipientFromProject($project);
        EmailQueue::enqueue(
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
    public function mailProjectRevisionRequested(Event $event, Project $project, string $note)
    {
        list($email, $name) = $this->getRecipientFromProject($project);
        $foo = [
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
        ];
        EmailQueue::enqueue(
            $email,
            $foo,
            [
                'subject' => self::getRevisionRequestedSubject(),
                'template' => 'application_revision_requested',
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
            ],
        );
    }

    public static function getRevisionRequestedSubject()
    {
        return self::$subjectPrefix . 'Revision Requested';
    }

    /**
     * @param Event $event
     * @param Project $project
     * @param string $note
     * @return void
     * @throws InternalErrorException
     */
    public function mailProjectRejected(Event $event, Project $project, string $note)
    {
        list($email, $name) = $this->getRecipientFromProject($project);
        EmailQueue::enqueue(
            $email,
            [
                'project' => $project,
                'fundingCycle' => $this->fundingCyclesTable->find('nextProject')->first(),
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
    public function mailProjectFunded(Event $event, Project $project, int $amount)
    {
        list($email, $name) = $this->getRecipientFromProject($project);
        EmailQueue::enqueue(
            $email,
            [
                'amount' => $amount,
                'project' => $project,
                'fundingCycle' => $this->fundingCyclesTable->get($project->funding_cycle_id),
                'myProjectsUrl' => Router::url([
                    'prefix' => 'My',
                    'controller' => 'Projects',
                    'action' => 'index'
                ], true),
                'supportEmail' => Configure::read('supportEmail'),
                'userName' => $name,
            ],
            [
                'subject' => self::$subjectPrefix . 'Application Funded',
                'template' => 'application_funded',
                'from_name' => $this->fromName,
                'from_email' => $this->fromEmail,
            ],
        );
    }

    /**
     * @param Event $event
     * @param Project $project
     * @return void
     * @throws InternalErrorException
     */
    public function mailProjectNotFunded(Event $event, Project $project)
    {
        list($email, $name) = $this->getRecipientFromProject($project);
        EmailQueue::enqueue(
            $email,
            [
                'project' => $project,
                'fundingCycle' => $this->fundingCyclesTable->find('nextProject')->first(),
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
}
