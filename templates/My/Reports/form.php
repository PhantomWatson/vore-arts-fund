<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Report $report
 * @var \App\Model\Entity\Project $project
 */
?>

<?= $this->Form->create($report, ['id' => 'report-form']) ?>

<fieldset>
    <div class="block-radio-buttons">
        <?= $this->Form->control(
            'is_final',
            [
                'type' => 'radio',
                'options' => [
                    0 => 'This is an <strong>update</strong> about an unfinished project.',
                    1 => 'This is the <strong>final report</strong> for a finished project.'
                ],
                'label' => 'What kind of a report is this?',
                'escape' => false,
            ]
        ) ?>
    </div>
    <?= $this->Form->control('body', ['label' => 'Report']) ?>
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
