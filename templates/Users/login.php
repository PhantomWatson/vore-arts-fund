<?php
/**
 * @var \App\View\AppView $this
 */
?>
<p>
    Don't have an account yet?
    <br />
    <?= $this->Html->link(
        'Register an account',
        [
            'controller' => 'Users',
            'action' => 'register',
        ],
        ['class' => 'btn btn-primary'],
    ) ?>
</p>
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
    <?= $this->Form->submit('Log in', ['class' => 'btn btn-primary']) ?>
    <?= $this->Html->link(
        'Forgot Password?',
        [
            'controller' => 'Users',
            'action' => 'forgotPassword',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
    <?= $this->Form->end() ?>
</div>
