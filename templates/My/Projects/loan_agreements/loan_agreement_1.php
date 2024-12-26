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
?>

<p>
    This loan agreement is made between <?= $project->user->name ?> (borrower) and the Vore Arts Fund (lender).
    Borrower agrees to borrow money from lender, who agrees to lend money to the borrower in agreement with the following terms.
</p>

<section class="loan-agreement">
    <h2>
        Loan
    </h2>

    <p>
        <strong>Loan Amount:</strong> <?= $project->amount_awarded_formatted ?>
    </p>

    <p>
        <strong>Interest Rate:</strong> This loan will not  bear interest. There shall be no interest associated with the borrowed money. The borrower’s only obligation to the lender is to repay the principal balance.
    </p>

    <p>
        <strong>Term:</strong> The total amount of the borrowed money shall be due and payable on <?= $dueDate ?>.
    </p>
</section>

<section class="loan-agreement">
    <h2>
        Payments
    </h2>

    <p>
        <strong>Payments:</strong>
        The borrower agrees to repay the lender the loan amount in full, whether in a single payment or in installments, by the due date. Payments can be made electronically through the Vore Arts Fund's website or made by checks mailed to and made out to Vore Arts Fund, Inc. The mailing address of the Vore Arts Fund can be found on its website.
    </p>

    <p>
        <strong>Prepayment:</strong>
        If the borrower makes a payment prior to the due date, there shall be no prepayment penalty.
    </p>

    <p>
        <strong>Overpayment:</strong>
        Any payment beyond the amount of the loan given to the borrower will be considered a donation to the Vore Arts Fund, and these donations can be used as a tax deduction. If you need the receipt of the donation, please reach out to the Vore Arts Fund.
    </p>

    <p>
        <strong>Failure To Make Payments:</strong>
        Through the mission of the Vore Arts Fund, if the borrower is unable to repay the loan, the unpaid portion will be forgiven upon the passage of the due date or upon written notification by the borrower that they are unable to repay. A forgiven loan will be reported to the IRS as taxable income for the borrower if the unpaid portion meets or exceeds the minimum amount required for reporting.
    </p>
</section>

<section class="loan-agreement">
    <h2>
        Miscellaneous
    </h2>

    <p>
        <strong>Severability:</strong>
        If any provision of this agreement or the application thereof shall, for any reason and to any extent, be invalid or unenforceable, neither the remainder of this agreement nor the application of the provision to other persons, entities, or circumstances shall be affected, thereby, but instead shall be enforced to the maximum extent permitted by the law.
    </p>

    <p>
        <strong>Governing Law:</strong>
        This agreement shall be construed and governed by the laws of the state of Indiana.
    </p>

    <p>
        <strong>Entire Agreement:</strong>
        This agreement contains all the terms agreed to by the parties relating to its subject matter, including any attachments or addenda. This agreement replaces all previous discussions, understanding, and oral agreements. The borrower and lender agree to the terms and conditions and shall be bound until the borrowed amount is paid in full or loan is canceled by the lender.
    </p>
</section>

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
