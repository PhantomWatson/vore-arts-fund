<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<?= $this->Form->create($user) ?>
    <?= $this->Form->control('password', ['value' => '']) ?>
    <?= $this->Form->control('confirm_password', ['type' => 'password', 'required' => true]) ?>
    <?= $this->Form->submit('Change Password', ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>
