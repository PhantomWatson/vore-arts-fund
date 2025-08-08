<?php
/**
 * @var string $projectTitle
 * @var string $userName
 * @var string $repaymentUrl
 * @var string $supportEmail
 * @var string $balance
 */
?>

<p>
    <?= $userName ?>,
</p>

<p>
    This is a friendly reminder that the loan for your project "<?= $projectTitle ?>" has <strong>an outstanding balance of <?= $balance ?></strong>. As a revolving loan fund, we depend on loan repayments to replenish our budget for awarding new loans to other projects like yours, so even partial repayments are appreciated.
</p>

<p>
    Once you're able to make a payment, please visit <a href="<?= $repaymentUrl ?>"><?= $repaymentUrl ?></a>.
</p>

<p>
    If you have any questions or need assistance, please don't hesitate to reach out to us at <a href="mailto:<?= $supportEmail ?>"><?= $supportEmail ?></a>.
</p>
