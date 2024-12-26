<?php
/**
 * @var \App\Model\Entity\Project $project
 */
$date = $project->loan_agreement_date
    ? $project->loan_agreement_date->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y')
    : (new \DateTime())->format('F j, Y');
$dueDate = $project->loan_due_date
    ? $project->loan_due_date->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y')
    : (new \DateTime(\App\Model\Entity\Project::DUE_DATE))->format('F j, Y');
$signed = (bool)$project->loan_agreement_date;
$amount = $project->amount_awarded_formatted;
?>

<p>
    This loan agreement is made between <?= $project->user->name ?> (borrower) and the Vore Arts Fund (lender).
    Borrower agrees to borrow money from lender, who agrees to lend money to the borrower in agreement with the following terms.
</p>

<?= $this->element('loan_terms', compact('amount', 'dueDate')) ?>

<?= $this->Form->create($project) ?>

<section class="loan-agreement">
    <h2>
        Signature
    </h2>

    <p>
        By entering the following information, you signify that you agree to these terms on behalf of the borrower and are authorized to do so.
        <br />
        For loans to <strong>individuals</strong>, enter the borrower's <strong>social security number</strong>
        <br />
        For loans to <strong>businesses</strong>, enter the borrower's <strong>federal employer identification number</strong>
    </p>

    <?= $this->Form->control('tin', ['type' => 'text', 'label' => 'Tax ID Number', 'class' => 'form-control']) ?>
    <?= $this->Form->control('tin_confirm', ['label' => 'Re-enter Tax ID Number', 'class' => 'form-control']) ?>

    <p>
        <strong>Agreement date:</strong> <?= $date ?>
    </p>
</section>

<input type="hidden" name="agreement" value="1" />

<?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>

<?= $this->Form->end() ?>
