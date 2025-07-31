<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Report $report
 * @var \App\Model\Entity\Project $project
 * @var string $rteJsPath
 */
if ($rteJsPath) {
    $this->Html->script($rteJsPath, ['block' => true, 'type' => 'module']);
}
?>

<?= $this->Form->create($report, ['id' => 'report-form']) ?>

<fieldset>
    <div class="block-radio-buttons">
        <?= $this->Form->control(
            'is_final',
            [
                'type' => 'radio',
                'options' => [
                    0 => 'This project is ongoing, and this is an <strong>update</strong> about it.',
                    1 => 'This project has concluded, and this is the <strong>final report</strong> for it.'
                ],
                'label' => 'What kind of a report is this?',
                'escape' => false,
            ]
        ) ?>
    </div>
    <?= $this->Form->textarea('body', ['label' => 'Report', 'data-rte-target' => 1]) ?>
    <div id="rte-root"></div>
</fieldset>

<?= $this->Form->button(
    'Submit',
    [
        'type' => 'submit',
        'class' => 'btn btn-primary',
    ]
) ?>
<?= $this->Form->end() ?>

<script>
    preventMultipleSubmit('#report-form');
</script>
