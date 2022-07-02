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

<div class="users form">
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->create(null, ['action' => 'forgot_password']) ?>
        <?= $this->Form->control(
            'User.email',
            [
                'label' => 'Email',
                'between' => '<br />',
                'type' => 'email',
                'required' => true,
            ]
        ) ?>
        <?= $this->Form->submit(
            'Send Password Reset Instructions',
            ['class' => 'submit btn btn-primary', 'id' => 'submit']
        ) ?>
        <?= $this->Form->end() ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
