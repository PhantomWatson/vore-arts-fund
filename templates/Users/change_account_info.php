<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$isAdmin = $user->is_admin;
?>

<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>
        Change Account Information
    </h1>
</div>

<div class="users form">
    <?= $this->Form->create() ?>
    <?= $this->Flash->render() ?>
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
