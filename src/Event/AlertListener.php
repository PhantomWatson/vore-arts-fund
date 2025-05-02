<?php

namespace App\Event;

use App\Alert\Alert;
use App\Alert\Slack;
use App\Model\Entity\Note;
use App\Model\Entity\Project;
use App\Model\Entity\Transaction;
use App\View\AppView;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class AlertListener implements EventListenerInterface
{
    private Slack $slack;

    public function __construct()
    {
        $this->alert = new Alert();
    }

    public function implementedEvents(): array
    {
        return [
            'Project.submitted' => 'alertProjectSubmitted',
            'Project.withdrawn' => 'alertProjectWithdrawn',
            'Stripe.chargeSucceeded' => 'alertStripeChargeSucceeded',
            'Mail.messageSentToApplicant' => 'alertMessageSentToApplicant',
            'Note.sentToApplicant' => 'alertNoteSentToApplicant',
            'Note.sentByApplicant' => 'alertNoteSentByApplicant',
        ];
    }

    public function alertProjectSubmitted(Event $event, Project $project)
    {
        $this->alert->addLine('Application submitted');

        $this->alert->addList([
            sprintf(
                'Project: <%s|%s>',
                Router::url([
                    'prefix' => 'Admin',
                    'controller' => 'Projects',
                    'action' => 'review',
                    'id' => $project->id
                ], true),
                Slack::encode($project->title),
            ),
            'Submitted by: ' . Slack::encode($project->user->name),
            'Requesting: ' . $project->amount_requested_formatted,
        ]);

        $this->alert->send(Alert::TYPE_APPLICATIONS);
    }

    public function alertProjectWithdrawn(Event $event, Project $project)
    {
        $this->alert->addLine('Application withdrawn');

        $this->alert->addLine(
            sprintf(
                'Project: <%s|%s>',
                Router::url([
                    'prefix' => 'Admin',
                    'controller' => 'Projects',
                    'action' => 'review',
                    'id' => $project->id
                ], true),
                Slack::encode($project->title),
            ),
        );

        $this->alert->send(Alert::TYPE_APPLICATIONS);
    }

    public function alertStripeChargeSucceeded(Event $event, string $payload, ?Transaction $transaction = null)
    {
        $this->alert->addLine('Stripe charge succeeded');

        $data = \json_decode($payload, true);
        $jsonError = \json_last_error();
        if ($data === null && $jsonError !== \JSON_ERROR_NONE) {
            $this->alert->addLine("Invalid payload: $payload");
            $this->alert->addline("json_last_error() was $jsonError)");
        } else {
            $chargeId = $data['data']['object']['id'] ?? false;
            $cents = $data['data']['object']['amount'] ?? false;
            $name = $data['data']['object']['metadata']['name'] ?? false;
            $email = $data['data']['object']['billing_details']['email'] ?? false;
            $this->alert->addList([
                'Charge ID: ' . ($chargeId === false ? 'Unknown' : $chargeId),
                'Amount: ' . (
                $cents === false
                    ? 'Unknown'
                    : ('$' . number_format(round($cents / 100, 2)))
                ),
                'Name: ' . ($name === false ? 'Unknown' : $name),
                'Email: ' . ($email === false ? 'Unknown' : $email),
            ]);
            $this->alert->addLine('');
            if (!$transaction) {
                $this->alert->addLine('*Error: No transaction recorded in database*');
            }
        }

        $this->alert->send(Alert::TYPE_TRANSACTIONS);
    }

    public function alertMessageSentToApplicant(Event $event, $email, $subject, $viewVars, $template)
    {
        $alertBody = $this->getRenderedView($viewVars, $template);
        $this->alert->addLine("*$subject*");
        $this->alert->addLine("Sent to $email");
        $this->alert->addLine('> ' . str_replace("\n", "\n> ", $alertBody));

        $this->alert->send(Alert::TYPE_APPLICANT_COMMUNICATION);
    }

    private function getRenderedView($viewVars, $template)
    {
        $view = new AppView();
        $view->disableAutoLayout();
        $templatePath = 'email' . DS . 'text' . DS . $template;
        $view->set($viewVars);
        return $view->render($templatePath);
    }

    public function alertNoteSentToApplicant($event, Note $note)
    {
        $sender = TableRegistry::getTableLocator()->get('Users')->get($note->user_id);
        $project = TableRegistry::getTableLocator()->get('Projects')->get($note->project_id, [
            'contain' => ['Users']
        ]);
        $this->alert->addLine(sprintf(
            'Message sent from %s to applicant %s regarding project "%s":',
            $sender->name,
            $project->user->name,
            $project->title,
        ));
        $this->alert->addLine('>' . str_replace("\n", "\n> ", $note->body));
        $this->alert->send(Alert::TYPE_APPLICANT_COMMUNICATION);
    }

    public function alertNoteSentByApplicant($event, Note $note)
    {
        $sender = TableRegistry::getTableLocator()->get('Users')->get($note->user_id);
        $project = TableRegistry::getTableLocator()->get('Projects')->get($note->project_id);
        $this->alert->addLine(sprintf(
            'Message received from applicant %s regarding project "%s":',
            $sender->name,
            $project->title,
        ));
        $this->alert->addLine('>' . str_replace("\n", "\n> ", $note->body));
        $this->alert->send(Alert::TYPE_APPLICANT_COMMUNICATION);
    }
}
