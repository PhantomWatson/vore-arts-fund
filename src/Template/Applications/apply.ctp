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
        <h1>Apply</h1>
        <div>
            <?= $this->Flash->render() ?>
            <?= $this->Form->create() ?>
            <fieldset>
                <legend><?= __('Please enter the following information') ?></legend>
                <?= $this->Form->control('title', ['required' => true]) ?>
                <?= $this->Form->control('description', ['type' => 'textarea', 'required' => true]) ?>
                <?= $this->Form->label('Category') ?>
                <?= $this->Form->select('category', ['Art', 'Music', 'Theatre', 'Other'], ['empty' => true], ['required' => true]) ?>
                <?= $this->Form->control('amount_requested', ['type' => 'number', 'required' => true]) ?>
                <?= $this->Form->label('Accept Partial Payout') ?>
                <?= $this->Form->radio('accept_partial_payout', ['Yes', 'No'], ['required' => true]) ?>
            </fieldset>
            <?= $this->Form->button(__('Save'), array('name' => 'save')); ?>
            <?= $this->Form->submit(__('Submit')); ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</body>

</html>