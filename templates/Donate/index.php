<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User|null $user
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

<p>
    If you wish to donate by check, please make payment out to Vore Arts Fund, Inc. and mail it to PO Box 1604, Muncie,
    Indiana, 47308.
</p>

<div class="row row--donate">
    <div class="col">
        <section class="card card--donate">
            <div class="card-body">
                <h1 class="card-title">
                    Make a one-time donation
                </h1>
                <?= $this->Form->create(null, ['id' => 'donate-index', 'method' => 'post', 'action' => $formAction]) ?>
                <?= $this->Form->control('name', ['label' => 'Name (optional)', 'value' => $user?->name]) ?>
                <div class="row">
                    <div class="col form-group">
                        <input type="hidden" name="coverProcessingFee" value="0" />
                        <label>
                            <input type="checkbox" name="coverProcessingFee" id="coverProcessingFee" value="1" />
                            Also cover 2.9% + 30¢ payment processing fee
                            <button id="coverProcessingFeePopover"
                                    type="button"
                                    class="btn btn-lg btn-link"
                                    data-bs-toggle="popover"
                                    title="What does this mean?"
                                    data-bs-title="What does this mean?"
                                    data-bs-content="Our payment processor charges us a fee for every donation, which you could generously volunteer to pay for so we receive 100% of the amount that you enter."
                                    data-bs-placement="top">
                                <i class="fa-solid fa-circle-question"></i>
                            </button>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group">
                        <p class="input-group">
                            <span class="input-group-text">$</span>
                            <input name="amount" type="number" class="form-control" aria-label="Amount to donate" min="1" id="donation-amount" required="required">
                            <span class="input-group-text">.00</span>
                        </p>
                    </div>
                    <div class="col">
                        <p>
                            <?= $this->Form->submit(__('Continue to payment information'), ['class' => 'btn btn-primary']) ?>
                        </p>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </section>
    </div>
    <div class="col">
        <section class="card card--donate">
            <div class="card-body">
                <h1 class="card-title">
                    Support with monthly donations
                </h1>
                <p class="card-text">
                    Any support is appreciated, but the best way to help us keep this program growing is to become a supporter
                    through <strong>Patreon</strong> and contribute monthly donations.
                </p>
                <p class="card-text">
                    <a href="https://www.patreon.com/VoreArtsFund" class="btn btn-primary">
                        Sign up for recurring donations
                    </a>
                </p>
            </div>
        </section>
    </div>
</div>

<script>
    const form = document.getElementById('donate-index');
    form.addEventListener('submit', (event) => {
        const amount = document.getElementById('donation-amount');
        if (parseInt(amount.value) <= 0) {
            event.preventDefault();
        }
    });

    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
</script>
