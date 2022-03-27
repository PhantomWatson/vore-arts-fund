<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $title
 */
?>

<?= $this->title() ?>

<div class="users form" id="register-form">
    <p>
        Register an account in order to vote or to submit applications for funding
    </p>
    <?= $this->Form->create($user) ?>
    <fieldset>
        <?= $this->Form->control('name', ['id' => 'register-name']) ?>
        <p class="footnote">
            This can be your name or the name of your organization
        </p>
        <?= $this->Form->control('email', ['type' => 'email']) ?>
        <?= $this->Form->control('password') ?>
        <?= $this->Form->control('phone', [
            'type' => 'tel',
            'minLength' => 10,
            'label' => 'Phone number',
            'placeholder' => 'Cell number with area code, e.g. 765-123-4567'
        ]) ?>
        <p class="footnote">
            We only use this to verify your account and prevent multi-account abuse
        </p>
    </fieldset>
    <?= $this->Form->submit('Register', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
