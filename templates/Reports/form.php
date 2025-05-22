<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Report $report
 * @var \App\Model\Entity\Project $project
 */
?>
<div class="alert alert-info">
    <p>
        We (and the IRS) require reports for every project that receives funding. We'd love to get an update whenever you have incremental victories to celebrate or challenges to share, but at a minimum, we need a report to be submitted at the conclusion of your project and at least one report per year if you have a multi-year project.
    </p>

    <p>
        These reports don't need to be long or formal, but should address the current state of the project and how you've used your funding. These will be shared with the public, so don't include any private or irrelevant information.
    </p>
</div>

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
