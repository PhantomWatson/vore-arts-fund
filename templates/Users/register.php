<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $title
 */
$this->append(
    'script',
    '<script src="https://www.google.com/recaptcha/api.js" async defer></script>',
);
?>

<div class="users form" id="register-form">
    <p>
        You'll need to register an account in order to vote or to submit applications for funding.
        Already have an account? <?= $this->Html->link(
            'Log in',
            \App\Application::LOGIN_URL,
        ) ?>
    </p>
    <?= $this->Form->create($user, ['id' => 'register-form']) ?>
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
        <div class="g-recaptcha" data-sitekey="<?= \Cake\Core\Configure::read('recaptcha.siteKey') ?>"></div>
        <button type="submit" class="btn btn-primary">
            Register
        </button>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<script>
    preventMultipleSubmit('#register-form');
</script>
