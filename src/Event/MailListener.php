<?php

namespace App\Event;

use App\Model\Entity\Application;
use App\Model\Entity\User;
use App\Model\Table\FundingCyclesTable;
use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class MailListener implements EventListenerInterface
{
    /** @var Mailer */
    private Mailer $mailer;
    public static string $subjectPrefix = 'Vore Arts Fund - ';
    private UsersTable $usersTable;
    private FundingCyclesTable $fundingCyclesTable;

    public function __construct()
    {
        $this->mailer = new Mailer('default');
        $this->mailer
            ->setFrom(Configure::read('noReplyEmail'), 'Vore Arts Fund')
            ->setEmailFormat('both');
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

    /**
     * @param Application $application
     * @return bool
     */
    private function setApplicantRecipient(Application $application): bool
    {
        $recipient = $this->getRecipientFromApplication($application);
        if (!$recipient) {
            return false;
        }
        list($email, $name) = $recipient;
        $this->mailer
            ->setTo($email, $name)
            ->setViewVars(['userName' => $name]);


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
        if (!$this->setApplicantRecipient($application)) {
            return;
        }
        $fundingCycle = $this->fundingCyclesTable->get($application->funding_cycle_id);
        $this->mailer
            ->setSubject(self::$subjectPrefix . 'Application Accepted')
            ->setViewVars(compact('application', 'fundingCycle'));
        $this->mailer->viewBuilder()
            ->setTemplate('application_accepted');
        $this->mailer->deliver();
    }

    public function mailApplicationRevisionRequested(Event $event, Application $application, string $note)
    {
        if (!$this->setApplicantRecipient($application)) {
            return;
        }
        $fundingCycle = $this->fundingCyclesTable->get($application->funding_cycle_id);
        $url = Router::url([
            'controller' => 'Applications',
            'action' => 'edit',
            'id' => $application->id,
        ], true);
        $this->mailer
            ->setSubject(self::$subjectPrefix . 'Revision Requested')
            ->setViewVars(compact('application', 'fundingCycle', 'note', 'url'));
        $this->mailer->viewBuilder()
            ->setTemplate('application_revision_requested');
        $this->mailer->deliver();
    }

    public function mailApplicationRejected(Event $event, Application $application, string $note)
    {
        if (!$this->setApplicantRecipient($application)) {
            return;
        }
        $fundingCycle = $this->fundingCyclesTable->find('nextApplication')->first();
        $this->mailer
            ->setSubject(self::$subjectPrefix . 'Application Not Accepted')
            ->setViewVars(compact('application', 'note', 'fundingCycle'));
        $this->mailer->viewBuilder()
            ->setTemplate('application_rejected');
        $this->mailer->deliver();
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
