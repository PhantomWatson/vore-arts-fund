<?php
/**
 * @var \App\View\AppView $this
 */
?>
<h1>Change Your Password</h1>
<?php echo $this->Form->create(null, array('action' => 'reset_password_token', 'id' => 'web-form')); ?>
    <?php echo $this->Form->hidden('User.reset_password_token'); ?>
	<?php echo $this->Form->input('User.new_passwd',  array('type' => 'password', 'label' => 'Password', 'between' => '<br />', 'type' => 'password') ); ?>
	<?php echo $this->Form->input('User.confirm_passwd',  array('type' => 'password', 'label' => 'Confirm Password', 'between' => '<br />', 'type' => 'password') ); ?>
	<?php echo $this->Form->submit('Change Password', array('class' => 'submit', 'id' => 'submit')); ?>
<?php echo $this->Form->end(); ?>
