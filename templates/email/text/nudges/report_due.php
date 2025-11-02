<?php
/**
 * @var string $projectTitle
 * @var string $userName
 * @var string $reportUrl
 * @var string $supportEmail
 * @var ?string $repaymentUrl Null if the loan is fully repaid
 * @var string $deadline
 */
?>

<?= $userName ?>,

The deadline is approaching to submit a report for <?= $projectTitle ?>. Please visit <?= $reportUrl ?> before <?= $deadline ?> to continue being qualified to apply for funding through the Vore Arts Fund in the future.

<?php if ($repaymentUrl): ?>
And remember, once you're able to make a payment on your loan, head over to <?= $repaymentUrl ?>.
<?php endif; ?>

If you have any questions or need assistance, please don't hesitate to reach out to us at <?= $supportEmail ?>.
