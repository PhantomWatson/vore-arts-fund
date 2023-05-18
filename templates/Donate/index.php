<?php
/**
 * @var \App\View\AppView $this
 */

$formAction = \Cake\Routing\Router::url([
    'controller' => 'Donate',
    'action' => 'payment',
]);
?>
<p>
    The Vore Arts Fund is a 501(c)(3) not-for-profit corporation, and donations to it are 100%
    <strong>tax-deductible</strong>. If you intend to claim a tax deduction resulting from a donation for $250 or more,
    you are more than welcome to <?= $this->Html->link('contact us', ['action' => 'contact']) ?>
    and request a donation confirmation letter for your records.
</p>

<?= $this->Form->create(null, ['id' => 'donate-index', 'method' => 'post', 'action' => $formAction]) ?>
    <div class="input-group mb-3">
        <span class="input-group-text">$</span>
        <input name="amount" type="number" class="form-control" aria-label="Amount to donate" min="1" id="donation-amount" required="required">
        <span class="input-group-text">.00</span>
    </div>
<?= $this->Form->submit(__('Continue to payment information'), ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>

<script>
    const form = document.getElementById('donate-index');
    form.addEventListener('submit', (event) => {
        const amount = document.getElementById('donation-amount');
        if (parseInt(amount.value) <= 0) {
            event.preventDefault();
        }
    });
</script>
