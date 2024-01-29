<?php
/**
 * @var \App\View\AppView $this
 * @var int $amount
 * @var string $name
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Html->script('https://js.stripe.com/v3/', ['block' => true]);
$this->Html->script('checkout.js', ['block' => true, 'defer' => true]);
?>

<div id="checkout">
    <p>
        <strong>Name:</strong> <?= $name ?: '(anonymous)' ?>
        <br />
        <strong>Donation amount:</strong> $<?= number_format($amount / 100, 2) ?>
        (<?= $this->Html->link(
            'change',
            ['action' => 'index']
        ) ?>)
    </p>

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
        <span id="button-text">Process payment</span>
    </button>
</form>

<div id="payment-message" class="visually-hidden"></div>

<script>
    window.stripeAmount = <?= json_encode($amount) ?>;
    window.stripeDonorName = <?= json_encode($name ?: null) ?>;
    window.stripeReturnUrl = <?= json_encode(Router::url([
        'prefix' => false,
        'controller' => 'Donate',
        'action' => 'complete',
    ], true)) ?>;
    if (typeof Stripe === 'function') {
        window.stripe = Stripe(<?= json_encode(Configure::read('Stripe.publishable_key')) ?>);
    }
</script>
