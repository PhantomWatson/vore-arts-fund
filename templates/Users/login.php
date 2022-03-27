<?php
/**
 * @var \App\View\AppView $this
 */
?>

<?= $this->title() ?>

<div class="users form" id="login-form">
    <?= $this->Flash->render() ?>
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->control('email') ?>
        <?= $this->Form->control('password', ['value' => '']) ?>
    </fieldset>
    <?= $this->Form->submit(__('Login'), ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
    <?= $this->Html->link(
        'Forgot Password?',
        [
            'controller' => 'Users',
            'action' => 'forgotPassword',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</div>
