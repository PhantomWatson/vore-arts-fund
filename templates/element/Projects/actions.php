<?php
/**
 * @var Project $project
 */

use App\Model\Entity\Project;

?>
<?php if ($project->isUpdatable()): ?>
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
<?php endif; ?>
<?php if ($project->canTransitionTo(Project::STATUS_DELETED)): ?>
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
<?php if ($project->canTransitionTo(Project::STATUS_WITHDRAWN)): ?>
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
<?php if ($project->isReportable()): ?>
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
