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
    <h1>Withdraw</h1>
</div>

<?= $this->Form->create() ?>

<h4> Are you sure you want to withdraw? </h4>
<?= $this->Form->submit(__('Yes'), ['class' => 'btn btn-primary']) ?>
<?= $this->Html->link(
    'Back',
    [
        'controller' => 'Users',
        'action' => 'myAccount',
    ],
    ['class' => 'btn btn-secondary']
) ?>
<?= $this->Form->end() ?>
