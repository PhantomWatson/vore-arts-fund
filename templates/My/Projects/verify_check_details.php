<?php
/**
 * @var Project $project
 * @var \App\View\AppView $this
 */

use App\Model\Entity\Project;

$defaultFormTemplate = include(CONFIG . 'bootstrap_form.php');
?>

<p>
    Before reviewing and signing our loan agreement, please double-check the following information and make any updates that are needed.
</p>


<?= $this->Form->create($project) ?>

<?= $this->Form->control('check_name', ['label' => 'Legal name of the individual or business who is receiving this loan']) ?>

<div class="form-group required">
    <label for="amount-requested">
        Mailing address
    </label>
    <?php $this->Form->setTemplates([
        'formGroup' => '{{input}}',
        'inputContainer' => '{{content}}',
    ]); ?>
    <div class="input-group mb-3">
        <?= $this->Form->control(
            'address',
            [
                'placeholder' => '123 N. Example Blvd.',
                'required' => true,
            ]
        ) ?>
        <span class="input-group-text" id="address-postfix">, Muncie, IN</span>
        <?php $this->Form->setTemplates([
            'formGroup' => $defaultFormTemplate['formGroup'],
            'inputContainer' => $defaultFormTemplate['inputContainer'],
        ]); ?>
    </div>
    <?= $this->Form->control(
        'zipcode',
        ['label' => 'ZIP code', 'pattern' => Project::ZIPCODE_REGEX]
    ) ?>
</div>
<input type="hidden" name="setup" value="1" />
<?= $this->Form->submit('Next', ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>
