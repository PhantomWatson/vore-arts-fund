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
    <?= $this->Form->button(__('Save')); ?>
    <?= $this->Form->submit(__('Submit')); ?>
    <?= $this->Form->end() ?>

    </div>

</body>
</html>
