<?php

namespace App\Event;

use App\Alert\Alert;
use App\Alert\Slack;
use App\Model\Entity\Project;
use App\Model\Entity\Transaction;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
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
            if ($transaction) {
                $this->alert->addLine('Recorded transaction info:');
                $transactionUrl = Router::url(
                    [
                        'prefix' => 'Admin',
                        'controller' => 'Transactions',
                        'action' => 'view',
                        'id' => $transaction->id,
                    ],
                    true
                );
                $project = $transaction->project_id
                    ? sprintf(
                        '<%s|%s>',
                        Router::url(
                            [
                                'prefix' => 'Admin',
                                'controller' => 'Projects',
                                'action' => 'review',
                                'id' => $transaction->project_id,
                            ],
                            true
                        ),
                        $transaction->project->title,
                    )
                    : 'none';
                $this->alert->addList([
                    "<$transactionUrl|View transaction>",
                    'Transaction type: ' . $transaction->type_name,
                    "Associated project: $project",
                    'Net amount: ' . ($transaction->dollar_amount_net_formatted),
                ]);
            } else {
                $this->alert->addLine('*Error: No transaction recorded in database*');
            }
        }

        $this->alert->send(Alert::TYPE_TRANSACTIONS);
    }
}
