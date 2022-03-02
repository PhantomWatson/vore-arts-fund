<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $title
 */
?>

<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>
        <?= $title ?>
    </h1>
</div>

<div class="users form" id="register-form">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <?= $this->Form->control('email', ['type' => 'email']) ?>
        <?= $this->Form->control('password') ?>
        <?= $this->Form->control('name') ?>
        <?= $this->Form->control('phone', [
            'type' => 'tel',
            'minLength' => 10,
            'label' => 'Phone number',
        ]) ?>
        <p class="footnote">
            (with area code, e.g. 765-123-4567)
        </p>
    </fieldset>
    <?= $this->Form->submit(__('Submit'), ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
