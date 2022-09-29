<?php

namespace App\Event;

use App\Model\Entity\Application;
use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;

class MailListener implements EventListenerInterface
{
    /** @var Mailer */
    private Mailer $mailer;
    public static string $subjectPrefix = 'Vore Arts Fund - ';

    public function __construct()
    {
        $this->mailer = new Mailer('default');
        $this->mailer->setFrom(Configure::read('noReplyEmail'), 'Vore Arts Fund');
    }

    /**
     * @param Application $application
     * @return array|false
     */
    private function getRecipientFromApplication(Application $application)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');

        try {
            /** @var User $user */
            $user = $usersTable->get($application->user_id ?? false);
            return [$user->email, $user->name];
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            Log::error("Application #{$application->id} does not have a valid user ID (#$application->user_id)");
            return false;
        }
    }

    /**
     * @param Application $application
     * @return bool
     */
    private function setApplicantRecipient(Application $application)
    {
        $recipient = $this->getRecipientFromApplication($application);
        if (!$recipient) {
            return false;
        }
        list($email, $name) = $recipient;
        $this->mailer->setTo($email, $name);

        return true;
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

    public function mailApplicationAccepted(Event $event, Application $application)
    {
        $this->mailer->setSubject(self::$subjectPrefix . 'Application Accepted');
        if (!$this->setApplicantRecipient($application)) {
            return;
        }
    }

    public function mailApplicationRevisionRequested(Event $event, Application $application)
    {
        $this->mailer->setSubject(self::$subjectPrefix . 'Revision Requested');
        if (!$this->setApplicantRecipient($application)) {
            return;
        }
    }

    public function mailApplicationRejected(Event $event, Application $application)
    {
        $this->mailer->setSubject(self::$subjectPrefix . 'Application Rejected');
        if (!$this->setApplicantRecipient($application)) {
            return;
        }
    }

    public function mailApplicationFunded(Event $event, Application $application)
    {
        $this->mailer->setSubject(self::$subjectPrefix . 'Application Funded');
        if (!$this->setApplicantRecipient($application)) {
            return;
        }
    }

    public function mailApplicationNotFunded(Event $event, Application $application)
    {
        $this->mailer->setSubject(self::$subjectPrefix . 'Application Not Funded');
        if (!$this->setApplicantRecipient($application)) {
            return;
        }
    }
}
