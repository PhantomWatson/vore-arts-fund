<p>Dear <?php echo $User['name']; ?>,</p>

<p>You may change your password using the link below.</p>
<?php $url = 'localhost:8765/users/reset_password_token/' . $User['reset_password_token']; ?>

<p>Your password won't change until you access the link above and create a new one.</p>
<p>Thanks and have a nice day!</p>