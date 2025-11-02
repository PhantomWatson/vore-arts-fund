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

<p>
    <?= $userName ?>,
</p>

<p>
    <strong>The deadline is approaching to submit a report for <?= $projectTitle ?></strong>. Please visit <a href="<?= $reportUrl ?>"><?= $reportUrl ?></a> before <strong><?= $deadline ?></strong> to continue being qualified to apply for funding through the Vore Arts Fund in the future.
</p>

<?php if ($repaymentUrl): ?>
    <p>
        And remember, once you're able to make a payment on your loan, head over to <a href="<?= $repaymentUrl ?>"><?= $repaymentUrl ?></a>.
    </p>
<?php endif; ?>

<p>
    If you have any questions or need assistance, please don't hesitate to reach out to us at <a href="mailto:<?= $supportEmail ?>"><?= $supportEmail ?></a>.
</p>
