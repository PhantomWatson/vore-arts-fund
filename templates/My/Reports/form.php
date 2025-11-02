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

<fieldset class="report-form">
    <div class="block-radio-buttons">
        <strong>What kind of a report is this?</strong>
        <?= $this->Form->radio(
            'is_final',
            [
                0 => 'This project is ongoing, and this is an <strong>update</strong> about it.',
                1 => 'This project has concluded, and this is the <strong>final report</strong> for it.'
            ],
            [
                'escape' => false,
            ]
        ) ?>
    </div>
    <p class="alert alert-warning" id="final-report-warning" style="display: none;">
        Once you submit a <strong>final report</strong>, the project will be considered concluded, and you will not be able to submit further updates for it.
    </p>
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
    const finalOption = document.querySelector('input[name="is_final"][value="1"]');
    const options = document.querySelectorAll('input[name="is_final"]');
    const warning = document.getElementById('final-report-warning');
    const toggleWarning = () => {
        if (finalOption.checked) {
            warning.style.display = 'block';
        } else {
            warning.style.display = 'none';
        }
    };
    options.forEach(option => option.addEventListener('change', e => {
        toggleWarning();
    }));
    toggleWarning();

    // Handle back/forward cache restoration
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            toggleWarning();
        }
    });
</script>
