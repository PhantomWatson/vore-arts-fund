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
?>
<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>Apply</h1>
</div>
<div class = "apply">
    <?= $this->Flash->render() ?>
    <?= $this->Form->create(null, ['enctype' => 'multipart/form-data']) ?>
    <fieldset>
        <legend><?= __('Please enter the following information') ?></legend>
        <?= $this->Form->control('title', ['required' => true]) ?>
        <?= $this->Form->control('description', ['type' => 'textarea', 'required' => true]) ?>
        <?= $this->Form->label('Category') ?>
        <?= $this->Form->select(
            'category',
            ['Film', 'Visual', 'Performance', 'Literature','Music'],
            ['empty' => false, 'required' => true]
        ) ?>
        <?= $this->Form->control('amount_requested', ['type' => 'number', 'required' => true]) ?>
        <?= $this->Form->label('Accept Partial Payout') ?>
        <?= $this->Form->radio('accept_partial_payout', ['Yes', 'No'], ['required' => true]) ?>
        <?= $this->Form->file('image') ?>
        <?= $this->Form->control('imageCaption') ?>
    </fieldset>
    <?= $this->Form->button(__('Save'), ['name' => 'save']) ?>
    <?= $this->Form->button('Submit', ['type' => 'submit']) ?>
    <?= $this->Form->end() ?>
</div>
