<?php
/**
 * @var \App\View\AppView $this
 */
?>

<div class='pb-2 mt-4 mb-2 border-bottom'>
  <h1>Login</h1>
</div>

<div class="users form">
  <?= $this->Flash->render() ?>
  <?= $this->Form->create() ?>
  <fieldset>
      <?= $this->Form->control('email') ?>
      <?= $this->Form->control('password') ?>
  </fieldset>
  <?= $this->Form->button(__('Login')) ?>
  <?= $this->Form->end() ?>
  <?= $this->Html->link('Forgot Password?', '/forgot-password', ['class' => 'button']) ?>
</div>
