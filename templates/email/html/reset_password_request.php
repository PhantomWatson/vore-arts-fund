<?php
/**
 * @var \App\Model\Entity\User $User
 * @var \App\View\AppView $this
 */
use Cake\Routing\Router;

$url = Router::url([
    'controller' => 'Users',
    'action' => 'resetPasswordToken',
    $User->reset_password_token
], true);
?>

<p>Dear <?= $User->name ?>,</p>

<p>You may change your password using the link below.</p>
<p><a href="<?= $url ?>"><?= $url ?></a></p>

<p>Your password won't change until you access the link above and create a new one.</p>
<p>Thanks and have a nice day!</p>
