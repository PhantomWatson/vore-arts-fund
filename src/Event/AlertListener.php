<?php

namespace App\Event;

use App\Alert\Alert;
use App\Alert\Slack;
use App\Model\Entity\Project;
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
                    $project->id
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
                    $project->id
                ], true),
                Slack::encode($project->title),
            ),
        );

        $this->alert->send(Alert::TYPE_APPLICATIONS);
    }
}
