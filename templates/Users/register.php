<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $title
 */
?>

<div class="users form" id="register-form">
    <p>
        Register an account in order to vote or to submit applications for funding
    </p>
    <?= $this->Form->create($user) ?>
    <fieldset>
        <div class="input-with-footnote">
            <?= $this->Form->control('name', ['id' => 'register-name']) ?>
            <p class="footnote">
                This can be your name or the name of your organization
            </p>
        </div>
        <?= $this->Form->control('email', ['type' => 'email']) ?>
        <?= $this->Form->control('password') ?>
        <?= $this->element('phone_input') ?>
    </fieldset>
    <?= $this->Form->submit('Register', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
