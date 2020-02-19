<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;

$this->layout = false;

$cakeDescription = 'CakePHP: the rapid development PHP framework';
$user = $this->request->getSession()->read('Auth.User');
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Flash->render() ?>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>
    </title>

    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('style.css') ?>
    <?= $this->Html->css('home.css') ?>
    <link href="https://fonts.googleapis.com/css?family=Raleway:500i|Roboto:300,400,700|Roboto+Mono" rel="stylesheet">
</head>
<body class="home">
    <h1>Change Account Information</h1>
    <?php
    $isAdmin = $user['is_admin'];
    ?>
       <div class="users form">
    <?= $this->Form->create('User') ?>
    <?= $this->Flash->render() ?>
        <fieldset>
            <legend><?= __('Enter information you are wanting to change, leave boxes empty if you do not wish to change information:') ?></legend>
           <h3>*Old Password can not be empty</h3>
            <?= $this->Form->control('email') ?>
            <?= $this->Form->control('name') ?>
            <?= $this->Form->control('phone') ?>
            <?= $this->Form->control('current password') ?>
            <?= $this->Form->control('new password') ?>

        </fieldset>
    <?= $this->Form->button(__('Submit')); ?>
    <?= $this->Form->end() ?>



</body>
</html>