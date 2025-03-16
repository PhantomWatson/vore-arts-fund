<?php
/**
 * @var \App\Model\Entity\Project $project
 */
$dueDate = (new \DateTime(\App\Model\Entity\Project::DUE_DATE))->format('F j, Y');
$amount = $project->amount_awarded_formatted;
?>

<?= $this->element('Projects/loan_agreement_preamble', compact('project')) ?>

<p>
    This loan agreement will remain available on this website for you to access after signing.
</p>

<section class="loan-agreement">
    <h2>
        Terms
    </h2>
    <?= $this->element('loan_terms', compact('amount', 'dueDate')) ?>
</section>

<?= $this->Form->create($project) ?>
<section class="loan-agreement">
    <h2>
        Tax ID number
    </h2>

    <?php if ($project->requires_tin): ?>
        <p>
            Because the amount of this loan meets the IRS's $600 threshold for reporting, we must collect your tax ID number so that in the event of the loan being forgiven, the forgiven amount can be reported as income.
        </p>
        <p>
            <br />
            For loans to <strong>individuals</strong>, enter the borrower's <strong>social security number</strong>
            <br />
            For loans to <strong>businesses</strong>, enter the borrower's <strong>federal employer identification number</strong>
        </p>

        <?= $this->Form->control('tin_provide', ['type' => 'text', 'label' => 'Tax ID Number', 'class' => 'form-control']) ?>
        <?= $this->Form->control('tin_confirm', ['label' => 'Re-enter Tax ID Number', 'class' => 'form-control']) ?>
    <?php else: ?>
        <p>
            Because the amount of this loan is lower than the IRS's $600 threshold for reporting, your tax ID number is not required.
        </p>
    <?php endif; ?>
</section>

<section class="loan-agreement">
    <h2>
        Signature
    </h2>
    <p>
        By entering your name, you signify that you agree to these terms on behalf of the borrower and are authorized to do so.
    </p>
    <?= $this->Form->control('loan_agreement_signature', ['label' => 'Type your full name', 'value' => '']) ?>
</section>

<?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>
