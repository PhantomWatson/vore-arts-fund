<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project[] $projects
 */

$projectOptions = [];
foreach ($projects as $project) {
    $projectOptions[$project->id] = $project->title;
}
?>

<?= $this->Form->create(
    null,
    [
        'url' => [
            'prefix' => 'My',
            'controller' => 'Reports',
            'action' => 'submit',
            '?' => ['selectingProject' => 1],
        ]
    ]
) ?>
<fieldset>
    <?= $this->Form->control('project_id', [
        'label' => 'Select Project',
        'options' => $projectOptions,
        'empty' => '',
        'class' => 'form-control'
    ]) ?>
</fieldset>
<?= $this->Form->button('Continue', ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>
