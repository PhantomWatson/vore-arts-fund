<?php
/**
 * @var \App\View\AppView $this
 */
?>

<?= $this->Form->postLink(
    'Resend phone number verification code',
    ['action' => 'verifyResend'],
    ['class' => 'btn btn-secondary']
) ?>

<?= $this->Form->create() ?>
<fieldset>
    <?= $this->Form->control('code', ['label' => 'Verification code']) ?>
    <?= $this->Form->submit(__('Verify'), ['class' => 'btn btn-primary']) ?>
</fieldset>
<?= $this->Form->end() ?>
