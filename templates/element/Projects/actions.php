<?php
/**
 * @var Project $project
 * @var AppView $this
 */

use App\Model\Entity\Project;
use App\View\AppView;
?>

<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        Actions
    </button>

    <ul class="dropdown-menu">
        <?php if ($project->isAgreeable() || $project->loan_agreement_date): ?>
            <?= $this->Html->link(
                $project->isAgreeable()
                    ? '<i class="fa-solid fa-pencil"></i> Submit loan agreement'
                    : '<i class="fa-solid fa-file-contract"></i> View loan agreement',
                [
                    'controller' => 'Projects',
                    'action' => 'loanAgreement',
                    'id' => $project->id,
                ],
                [
                    'class' => 'dropdown-item',
                    'escape' => false
                ]
            ) ?>
        <?php endif; ?>
        <?php if ($this->getRequest()->getParam('action') != 'view'): ?>
            <?= $this->Html->link(
                '<i class="fa-solid fa-eye"></i> View project',
                [
                    'controller' => 'Projects',
                    'action' => 'view',
                    'id' => $project->id,
                ],
                [
                    'class' => 'dropdown-item',
                    'escape' => false
                ]
            ) ?>
        <?php endif; ?>
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
                    '?' => [
                        'myProjects' => 1,
                    ],
                ],
                [
                    'class' => 'dropdown-item',
                    'escape' => false
                ]
            ) ?>
        <?php endif; ?>
    </ul>
</div>
