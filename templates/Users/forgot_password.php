<?php
/**
 * @var \App\View\AppView $this
 */
?>

<div class="users form">
    <fieldset>
        <?= $this->Form->create(null, ['id' => 'forgot-password-form']) ?>
        <?= $this->Form->control(
            'User.email',
            [
                'label' => 'Email',
                'between' => '<br />',
                'type' => 'email',
                'required' => true,
            ]
        ) ?>
        <button type="submit" class="btn btn-primary">
            Send password reset instructions
        </button>
        <?= $this->Form->end() ?>
    </fieldset>
</div>

<script>
    preventMultipleSubmit('#forgot-password-form');
</script>
