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

<?= $this->Form->create(null, ['id' => 'resubmit-form']) ?>

<h4> Are you sure you want to resubmit your application? </h4>
<button type="submit" class="btn btn-primary">
    Yes
</button>
<?= $this->Html->link(
    'Back',
    [
        'prefix' => false,
        'controller' => 'Users',
        'action' => 'account',
    ],
    ['class' => 'btn btn-secondary']
) ?>
<?= $this->Form->end() ?>

<script>
    preventMultipleSubmit('#resubmit-form');
</script>
