<?php
/**
 * @var \App\View\AppView $this
 * @var string $amount
 * @var string $date
 * @var string|null $donorName
 * @var string $supportEmail
 */
?>
<?= $donorName ?: 'Hello' ?>,

Thank you so much for your donation of <?= $amount ?> to the Vore Arts Fund on <?= h($date) ?>!
Your support is greatly appreciated and will help us continue to fund the arts in our community.

As your donation was solely to support our mission and not in exchange for goods or services, this contribution is fully tax-deductible to the extent allowed by federal law. If you intend to claim a deduction for your donation, please keep this email as a receipt for your records. Refer to IRS Publication 526 or consult a tax professional for more information on tax deductions for charitable contributions.

If you have any questions, please email <?= $supportEmail ?>.
