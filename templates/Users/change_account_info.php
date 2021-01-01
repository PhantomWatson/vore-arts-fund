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
$user = $this->request->getSession()->read('Auth.User');
$isAdmin = $user['is_admin'];
?>

<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>Change Account Information</h1>
</div>

<div class="users form">
    <?= $this->Form->create() ?>
    <?= $this->Flash->render() ?>
    <fieldset>
        <?= $this->Form->control('email') ?>
        <?= $this->Form->control('name') ?>
        <?= $this->Form->control('phone') ?>
        <?= $this->Form->control('current password') ?>
        <?= $this->Form->control('new password') ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
