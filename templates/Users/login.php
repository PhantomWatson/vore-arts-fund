<?php
/**
 * @var \App\View\AppView $this
 */
?>

<div class="users form" id="login-form">
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->control('email') ?>
        <?= $this->Form->control('password', ['value' => '']) ?>
        <div class="form-check">
            <label for="stay-logged-in" class="form-check-label">
                <input class="form-check-input" type="checkbox" name="remember_me" value="1" id="stay-logged-in" />
                Stay logged in
            </label>
        </div>
    </fieldset>
    <?= $this->Form->submit(__('Login'), ['class' => 'btn btn-primary']) ?>
    <?= $this->Html->link(
        'Forgot Password?',
        [
            'controller' => 'Users',
            'action' => 'forgotPassword',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
    <?= $this->Form->end() ?>

    <p>
        Don't have an account yet?
        <?= $this->Html->link(
            'Register an account',
            [
                'controller' => 'Users',
                'action' => 'register',
            ],
        ) ?>
    </p>
</div>
