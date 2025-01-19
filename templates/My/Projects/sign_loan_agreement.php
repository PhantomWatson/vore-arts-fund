<?php
/**
 * @var \App\Model\Entity\Project $project
 */
$dueDate = (new \DateTime(\App\Model\Entity\Project::DUE_DATE))->format('F j, Y');
$amount = $project->amount_awarded_formatted;
?>

<?= $this->element('Projects/loan_agreement_preamble', compact('project')) ?>

<?= $this->element('loan_terms', compact('amount', 'dueDate')) ?>

<?= $this->Form->create($project) ?>
<section class="loan-agreement">
    <h2>
        Tax ID number
    </h2>

    <p>
        By entering the following information, you signify that you agree to these terms on behalf of the borrower and are authorized to do so.
        <br />
        For loans to <strong>individuals</strong>, enter the borrower's <strong>social security number</strong>
        <br />
        For loans to <strong>businesses</strong>, enter the borrower's <strong>federal employer identification number</strong>
    </p>

    <?= $this->Form->control('tin_provide', ['type' => 'text', 'label' => 'Tax ID Number', 'class' => 'form-control']) ?>
    <?= $this->Form->control('tin_confirm', ['label' => 'Re-enter Tax ID Number', 'class' => 'form-control']) ?>
    <input type="hidden" name="agreement" value="1" />
</section>
<?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>
