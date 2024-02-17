<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<?= $this->element('Users/account_nav') ?>

<?php if ($user->is_verified): ?>
    <p class="alert alert-success verification__already-verified">
        Your phone number has already been verified
    </p>
<?php else: ?>
    <?= $this->Form->create(null, ['id' => 'account-info-form']) ?>
    <fieldset>
        <?= $this->Form->control(
            'code',
            ['label' => 'Please enter the verification code that was sent to your phone'],
        ) ?>
        <button type="submit" class="btn btn-primary">
            Verify
        </button>
    </fieldset>
    <?= $this->Form->end() ?>

    <?= $this->Form->postLink(
        'Resend phone number verification code',
        ['action' => 'verifyResend'],
        ['class' => 'btn btn-secondary']
    ) ?>

    <script>
        preventMultipleSubmit('#account-info-form');
    </script>
<?php endif; ?>
