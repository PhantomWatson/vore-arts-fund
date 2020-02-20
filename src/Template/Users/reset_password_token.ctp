<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<h1>Change Your Password</h1>
<?= $this->Form->create($user, ['id' => 'web-form']) ?>
	<?= $this->Form->control('password') ?>
	<?= $this->Form->control('confirm_password',  ['type' => 'password', 'required' => true]) ?>
	<?= $this->Form->submit('Change Password') ?>
<?= $this->Form->end() ?>
