<?php

namespace App\Event;

use App\Model\Entity\Application;
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
     * @param Application $application
     * @return array|false
     */
    private function getRecipientFromApplication(Application $application)
    {
        try {
            /** @var User $user */
            $user = $this->usersTable->get($application->user_id ?? false);
            return [$user->email, $user->name];
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            Log::error("Application #{$application->id} does not have a valid user ID (#$application->user_id)");
            return false;
        }
    }

    public function implementedEvents(): array
    {
        return [
            'Application.accepted' => 'mailApplicationAccepted',
            'Application.revisionRequested' => 'mailApplicationRevisionRequested',
            'Application.rejected' => 'mailApplicationRejected',
            'Application.funded' => 'mailApplicationFunded',
            'Application.notFunded' => 'mailApplicationNotFunded',
        ];
    }

    /**
     * @param Event $event
     * @param Application $application
     * @return void
     * @throws InternalErrorException
     */
    public function mailApplicationAccepted(Event $event, Application $application)
    {
        list($email, $name) = $this->getRecipientFromApplication($application);
        EmailQueue::enqueue(
            $email,
            [
                'application' => $application,
                'fundingCycle' => $this->fundingCyclesTable->get($application->funding_cycle_id),
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
     * @param Application $application
     * @param string $note
     * @return void
     * @throws InternalErrorException
     */
    public function mailApplicationRevisionRequested(Event $event, Application $application, string $note)
    {
        list($email, $name) = $this->getRecipientFromApplication($application);
        EmailQueue::enqueue(
            $email,
            [
                'application' => $application,
                'fundingCycle' => $this->fundingCyclesTable->get($application->funding_cycle_id),
                'note' => $note,
                'url' => Router::url([
                    'controller' => 'Applications',
                    'action' => 'edit',
                    'id' => $application->id,
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
     * @param Application $application
     * @param string $note
     * @return void
     * @throws InternalErrorException
     */
    public function mailApplicationRejected(Event $event, Application $application, string $note)
    {
        list($email, $name) = $this->getRecipientFromApplication($application);
        EmailQueue::enqueue(
            $email,
            [
                'application' => $application,
                'fundingCycle' => $this->fundingCyclesTable->find('nextApplication')->first(),
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
     * @param Application $application
     * @param int $amount
     * @return void
     * @throws InternalErrorException
     */
    public function mailApplicationFunded(Event $event, Application $application, int $amount)
    {
        list($email, $name) = $this->getRecipientFromApplication($application);
        EmailQueue::enqueue(
            $email,
            [
                'amount' => $amount,
                'application' => $application,
                'fundingCycle' => $this->fundingCyclesTable->get($application->funding_cycle_id),
                'myApplicationsUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Applications',
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
     * @param Application $application
     * @return void
     * @throws InternalErrorException
     */
    public function mailApplicationNotFunded(Event $event, Application $application)
    {
        list($email, $name) = $this->getRecipientFromApplication($application);
        EmailQueue::enqueue(
            $email,
            [
                'application' => $application,
                'fundingCycle' => $this->fundingCyclesTable->find('nextApplication')->first(),
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
