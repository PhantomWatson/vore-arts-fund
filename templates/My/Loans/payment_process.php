<?php
/**
 * @var \App\View\AppView $this
 * @var int $projectId
 * @var int $amountTowardLoan // in cents
 * @var int $totalAmount      // in cents
 * @var string $name
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Html->script('https://js.stripe.com/v3/', ['block' => true]);
$this->Html->script('checkout.js', ['block' => true, 'defer' => true]);
?>

<p>
    <?= $this->Html->link(
        'Back',
        [
            'prefix' => 'My',
            'controller' => 'Loans',
            'action' => 'payment',
            'id' => $projectId,
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>

<div id="checkout">
    <span id="page-loading-indicator" class="loading-indicator">
        <i class="fa-solid fa-spinner fa-spin-pulse" title="Loading"></i>
    </span>
</div>

<form id="payment-form" style="display: none;">
    <div id="link-authentication-element"></div>
    <div id="payment-element"></div>
    <button id="submit" class="btn btn-primary">
        <i class="fa-solid fa-spinner fa-spin-pulse" title="Loading" id="submit-loading-indicator"
           style="display: none"></i>
        <span id="button-text">Pay $<?= number_format($totalAmount / 100, 2) ?></span>
    </button>
</form>

<div id="payment-message" class="visually-hidden"></div>

<script>
    window.transactionType = <?= json_encode(\App\Model\Entity\Transaction::TYPE_LOAN_REPAYMENT) ?>;
    window.projectId = <?= json_encode($projectId) ?>;
    window.stripeAmount = <?= json_encode($totalAmount) ?>;
    window.payerName = <?= json_encode($name ?: null) ?>;
    window.stripeReturnUrl = <?= json_encode(Router::url([
        'prefix' => 'My',
        'controller' => 'Loans',
        'action' => 'paymentComplete',
    ], true)) ?>;
    if (typeof Stripe === 'function') {
        window.stripe = Stripe(<?= json_encode(Configure::read('Stripe.publishable_key')) ?>);
    }
</script>
