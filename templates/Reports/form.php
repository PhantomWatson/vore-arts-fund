<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Report $report
 * @var \App\Model\Entity\Project $project
 * @var string|null $back
 */

use Cake\Routing\Router;

$back = $back ?? Router::url([
    'controller' => 'Projects',
    'action' => 'index'
]);
?>

<?= $this->Html->link(
    'Back',
    $back,
    ['class' => 'btn btn-secondary']
) ?>

<?= $this->Form->create($report) ?>

<fieldset>
    <?= $this->Form->control('body') ?>
    <?= $this->Form->control(
        'is_final',
        [
            'label' => 'This is the <strong>final</strong> report for this project',
            'escape' => false,
        ]
    ) ?>
</fieldset>

<?= $this->Form->button(
    'Submit',
    [
        'type' => 'submit',
        'class' => 'btn btn-primary',
    ]
) ?>
<?= $this->Form->end() ?>
