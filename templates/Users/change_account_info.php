<?php
/**
 * @var \App\View\AppView $this
 */
?>

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
