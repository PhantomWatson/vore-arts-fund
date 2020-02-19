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

$user = $this->request->getSession()->read('Auth.User');
$isAdmin = $user['is_admin'];
$userID = $user['id'];
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #BA0C2F">
    <a class="navbar-brand" href="/">Vore Arts Fund</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <?= $this->Html->link('Home', '/', array('class' => 'nav-link')); ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->link('Vote', '/vote', array('class' => 'nav-link')); ?>
            </li>
            <?php
            if ($userID == null) {
            ?>
                <li class="nav-item">
                    <?= $this->Html->link('Register', '/register', array('class' => 'nav-link')); ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link('Login', '/login', array('class' => 'nav-link')); ?>
                </li>
                <?php
                if ($isAdmin == 1) : ?>
                    <li class="nav-item">

                        <?= $this->Html->link('Admin Page', '/admin-page', array('class' => 'nav-link')) ?>
                    </li>
                <?php endif;
            } else {
                ?>
                <li class="nav-item">
                    <?= $this->Html->link('My Account', '/my-account', array('class' => 'nav-link')); ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link('Apply', '/apply', array('class' => 'nav-link')); ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link('Log Out', '/logout', array('class' => 'nav-link')); ?>
                </li>
            <?php
            } ?>

        </ul>
    </div>
</nav>