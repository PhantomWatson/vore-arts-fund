<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$isAdmin = $user->is_admin;
?>

<?= $this->title() ?>

<div class="users form">
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->control('email') ?>
        <?= $this->Form->control('name') ?>
        <?= $this->Form->control('phone') ?>
        <?= $this->Form->control('current password') ?>
        <?= $this->Form->control('new password') ?>
    </fieldset>
    <?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
