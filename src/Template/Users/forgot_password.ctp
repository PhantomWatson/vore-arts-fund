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
?>
<!DOCTYPE html>
<html>

<head>
    <?= $this->element('head'); ?>
    <title>
        <?= $cakeDescription ?>
    </title>
</head>

<body class="home">
    <?= $this->element('navbar'); ?>
    <div class="container">
        <div class='pb-2 mt-4 mb-2 border-bottom'>
            <h1>Forgot Password</h1>
        </div>

        <div class="users form">
            <?= $this->Flash->render() ?>
            <?= $this->Form->create() ?>
            <fieldset>
                <?php echo $this->Form->create(null, array('action' => 'forgot_password', 'id' => 'web-form')); ?>
                <?php echo $this->Form->control('User.email', array('label' => 'Email', 'between' => '<br />', 'type' => 'email', 'required' => true)); ?>
                <?php echo $this->Form->submit('Send Password Reset Instructions', array('class' => 'submit', 'id' => 'submit')); ?>
                <?php echo $this->Form->end(); ?>
            </fieldset>
            <?= $this->Form->end() ?>
        </div>
    </div>
</body>

</html>