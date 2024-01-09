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
        <?= $this->Form->control('current password', ['type' => 'password']) ?>
        <?= $this->Form->control('new password', ['type' => 'password']) ?>
    </fieldset>
    <?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
