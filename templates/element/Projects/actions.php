<?php
/**
 * @var Project $project
 */

use App\Model\Entity\Project;

$updateWhen = [
    Project::STATUS_DRAFT,
    Project::STATUS_REVISION_REQUESTED,
];
$withdrawWhen = [
    Project::STATUS_UNDER_REVIEW,
    Project::STATUS_ACCEPTED,
];
$reportWhen = [
    Project::STATUS_AWARDED,
];
?>
<?php if (in_array($project->status_id, $updateWhen)): ?>
    <?= $this->Html->link(
        '<i class="fa-solid fa-pencil"></i> Update / Submit',
        [
            'controller' => 'Projects',
            'action' => 'edit',
            'id' => $project->id,
        ],
        [
            'class' => 'dropdown-item',
            'escape' => false
        ]
    ) ?>
    <?= $this->Form->postLink(
        '<i class="fa-solid fa-trash"></i> Delete',
        [
            'prefix' => 'My',
            'controller' => 'Projects',
            'action' => 'delete',
            'id' => $project->id,
        ],
        [
            'class' => 'dropdown-item',
            'confirm' => 'Are you sure you want to delete this application?',
            'escape' => false
        ]
    ) ?>
<?php endif; ?>
<?php if (in_array($project->status_id, $withdrawWhen)): ?>
    <?= $this->Form->postLink(
        Project::ICON_WITHDRAW . ' Withdraw',
        [
            'controller' => 'Projects',
            'action' => 'withdraw',
            'id' => $project->id,
        ],
        [
            'class' => 'dropdown-item',
            'escape' => false,
            'confirm' => 'Are you sure you want to withdraw this application?',
        ]
    ) ?>
<?php endif; ?>
<?php if (in_array($project->status_id, $reportWhen)): ?>
    <?= $this->Html->link(
        Project::ICON_REPORT . ' Submit report',
        [
            'prefix' => false,
            'controller' => 'Reports',
            'action' => 'submit',
            $project->id,
        ],
        [
            'class' => 'dropdown-item',
            'escape' => false
        ]
    ) ?>
<?php endif; ?>
<?php if (count($project->notes)): ?>
    <?= $this->Html->link(
        Project::ICON_MESSAGE . ' View messages',
        [
            'prefix' => 'My',
            'controller' => 'Projects',
            'action' => 'messages',
            $project->id,
        ],
        [
            'class' => 'dropdown-item',
            'escape' => false
        ]
    ) ?>
<?php endif; ?>
<?php if (count($project->reports)): ?>
    <?= $this->Html->link(
        Project::ICON_REPORT . ' View reports',
        [
            'prefix' => false,
            'controller' => 'Reports',
            'action' => 'project',
            $project->id,
        ],
        [
            'class' => 'dropdown-item',
            'escape' => false
        ]
    ) ?>
<?php endif; ?>
