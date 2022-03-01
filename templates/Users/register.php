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
 * @var \App\Model\Entity\User $user
 */
?>

<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>Register</h1>
</div>

<div class="users form" id="register-form">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <?= $this->Form->control('email', ['type' => 'email']) ?>
        <?= $this->Form->control('password') ?>
        <?= $this->Form->control('name') ?>
        <?= $this->Form->control('phone', [
            'type' => 'tel',
            'minLength' => 10,
            'label' => 'Phone number',
        ]) ?>
        <p class="footnote">
            (with area code, e.g. 765-123-4567)
        </p>
    </fieldset>
    <?= $this->Form->submit(__('Submit'), ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
