<?php
/**
 * @var \App\View\AppView $this
 */
?>

<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>Login</h1>
</div>

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
