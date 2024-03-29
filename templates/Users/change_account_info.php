<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->Html->script('account-form', ['block' => true]);
?>

<?= $this->element('Users/account_nav') ?>

<div class="users form">
    <?= $this->Form->create($user, ['id' => 'account-info-form']) ?>
    <fieldset>
        <?= $this->Form->control('email') ?>
        <?= $this->Form->control('name') ?>
        <?= $this->element('phone_input') ?>
    </fieldset>
    <button type="submit" class="btn btn-primary">
        Submit
    </button>
    <?= $this->Form->end() ?>
</div>

<script>
    preventMultipleSubmit('#account-info-form');

    const accountForm = new AccountForm({
        originalPhone: <?= json_encode($user->phone) ?>,
        isVerified: <?= json_encode($user->is_verified) ?>,
        verificationEnabled: <?= json_encode(\Cake\Core\Configure::read('enablePhoneVerification')) ?>
    })
</script>
