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

<center>
    <?php
    $isAdmin = $user['is_admin'];
    if ( $isAdmin == 1){
        echo $this->Html->link('Admin Page', '/admin-page', array('class' => 'button'));
    }
    echo $this->Html->link('Home', '/', array('class' => 'button'));
    echo $this->Html->link('Vote', '/vote', array('class' => 'button'));
    $userID = $user['id'];
    if ($userID == null){
        echo $this->Html->link('Register', '/register', array('class' => 'button'));
        echo $this->Html->link('Login', '/login', array('class' => 'button'));
    }else{
        echo $this->Html->link('My Account', '/my-account', array('class' => 'button'));
        echo $this->Html->link('Apply', '/apply', array('class' => 'button'));
        echo $this->Html->link('Login Out', '/logout', array('class' => 'button'));
    }
    ?>
</center>
</body>
</html>
