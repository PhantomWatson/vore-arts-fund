<?php
/**
 * @var \App\Model\Entity\Project $project
 */
$date = $project->loan_agreement_date
    ? $project->loan_agreement_date->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('M j, Y')
    : (new \DateTime())->format('M j, Y');
$dueDate = $project->loan_due_date
    ? $project->loan_due_date->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('M j, Y')
    : (new \DateTime(\App\Model\Entity\Project::DUE_DATE))->format('M j, Y');
?>

<p>
    Lendor: Vore Arts Fund, Inc.
</p>
<p>
    Lendee: <?= $project->user->name ?>
</p>

<p>
    The Vore Arts Fund is a 501(c)(3) not-for-profit program for supporting the Muncie, Indiana, arts community by distributing funds to cover the up-front costs of producing for-profit art, music, theater, and art education.
</p>

<h1>
    Loan
</h1>
This loan agreement is made between <?= $project->user->name ?> (borrower) and the Vore Arts Fund (lender).
Borrower agrees to borrow money from lender, who agrees to lend money to the borrower in agreement with the following terms.

Loan Amount: <?= $project->amount_awarded ?>

Interest Rate: This loan will NOT bear interest. There shall be no interest associated with the borrowed money. The borrower’s only obligation to the lender is to repay the principal balance.

Term: The total amount of the borrowed money shall be due and payable on <?= $dueDate ?>.

Payments:
The borrower agrees to repay the lender a payment of <?= $project->amount_awarded ?> in full, whether in a single payment or in installments, by the due date of <?= $dueDate ?>. Payments can be made electronically through the Vore Arts Fund's website or made by checks mailed to P.O. Box 1604, Muncie IN, 47308.
Any payment beyond the amount of the loan given to the borrower will be considered a donation to the Vore Arts Fund.
These donations can be used as a tax deduction. If you need the receipt of the donation, please reach out to the Vore Arts Fund.

Failure To Make Payments:
Through the mission of the Vore Arts Fund, if the borrower is unable to repay the loan, the unpaid portion will be forgiven upon the passage of the due date or upon written notification by the borrower that they are unable to repay. Forgiven loans to a borrower will be reported to the IRS as taxable income if over the course of the calendar year they individually or collectively meet or exceed a minimum amount required for reporting.

Prepayment:
If the borrower makes a payment prior to the due date, there shall be no prepayment penalty.

Severability:
If any provision of this agreement or the application thereof shall, for any reason and to any extent, be invalid or unenforceable, neither the remainder of this agreement nor the application of the provision to other persons, entities, or circumstances shall be affected, thereby, but instead shall be enforced to the maximum extent permitted by the law.

Governing Law:
This agreement shall be construed and governed by the laws of the state of Indiana.

Entire Agreement:
This agreement contains all the terms agreed to by the parties relating to its subject matter, including any attachments or addenda. This agreement replaces all previous discussions, understanding, and oral agreements. The borrower and lender agree to the terms and conditions and shall be bound until the borrowed amount is paid in full or loan is canceled by the lender.

Signatures:


____________________________________________			___________________
Authorized Representative of The Vore Arts Fund			Date

____________________________________________			____________________
Grantee								Date
Date: <?= $date ?>

The Vore Arts Fund
Donor Pledge Agreement
Date: [xx/xx/20xx]

The Vore Arts Fund is a 501(c)(3) not-for-profit corporation created to support the Muncie, Indiana, arts community by distributing funds to cover the up-front costs of producing for-profit art, music, theater, and art education.
By absorbing the financial risk inherent in art-related projects with up-front costs, this program makes it easier for artists, educators, performers, and promoters to improve the quality-of-place of our community. In the future, we hope to grow the fund so that it may also distribute grants to support noncommercial art endeavors, such as public murals, free performances, and free art classes.
Loan Repayment Information Summary:
I, the borrower, am repaying my loan amount of $___________ to the lender, the Vore Arts Fund, according to the following payment plan:
_____ One-time lump sum
_____ Multiple contributions
_____ Monthly
_____ Quarterly
_____ Annually
_____ Other (please explain) _______________________________________________
Borrower Information:
Name: ________________________________________________________
Address: ______________________________________________________
______________________________________________________
Phone Number: _______________________________________________
If you plan to repay by check, checks can be made payable to Vore Arts Fund, Inc. and mailed to:
Vore Arts Fund
P.O. Box 1604
Muncie, IN 47308.
Thank you so much for your support!
