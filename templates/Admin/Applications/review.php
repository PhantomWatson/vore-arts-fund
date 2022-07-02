<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 * @var string $title
 * @var string[] $statusOptions
 */
?>

<?= $this->Html->link(
    'Back',
    [
        'controller' => 'Admin',
        'action' => 'applications',
    ],
    ['class' => 'btn btn-secondary']
) ?>

<div>
    <h4>Status</h4>
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->control(
            'status_id',
            [
                'type' => 'select',
                'options' => $statusOptions,
                'label' => false,
                'empty' => 'Category',
                'default' => $application->status_id,
            ]
        ) ?>
    </fieldset>
    <?= $this->Form->submit(__('Update Status'), ['class' => 'btn btn-secondary']) ?>
    <?= $this->Form->end() ?>
</div>

<?= $this->element('../Applications/view') ?>

<form>
    <h4>Comment</h4>
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->textarea('Comment') ?>
    </fieldset>
    <?= $this->Form->submit(__('Comment'), ['class' => 'btn btn-secondary']) ?>
    <?= $this->Form->end() ?>
</form>
