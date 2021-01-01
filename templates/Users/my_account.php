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
use Cake\ORM\TableRegistry;

$user = $this->request->getSession()->read('Auth.User');
$applications = TableRegistry::getTableLocator()
    ->get('Applications')
    ->find()
    ->where(['user_id' => $user['id']])
    ->all()
    ->toArray();
?>

<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>My Account</h1>
</div>
<?= $this->Html->link(
    'Change Account Info',
    [
        'controller' => 'Users',
        'action' => 'changeAccountInfo',
    ],
    ['class' => 'button']
) ?>

<h2>Applications</h2>
<?php foreach ($applications as $application) { ?>
    <div>
        <h3><?= $application['title'] ?></h3>
        <?php if ($application['status_id'] === 8) {?>
            <p>Status: Withdrawn</p>
        <?php } ?>
        <?= $this->Html->link(
            'View',
            [
                'controller' => 'Applications',
                'action' => 'view',
                'id' => $application['id'],
                'slug' => '/view-application//',
            ],
            ['class' => 'button']
        ) ?>
        <?php if ($application['status_id'] !== 8) {
            echo $this->Html->link(
                'Withdraw',
                [
                    'controller' => 'Applications',
                    'action' => 'withdraw',
                    'id' => $application['id'],
                ],
                ['class' => 'button']
            );
        }?>
        <?php if ($application['status_id'] === 8) {
            echo $this->Html->link(
                'Resubmit',
                [
                    'controller' => 'Applications',
                    'action' => 'resubmit',
                    'id' => $application['id'],
                ],
                ['class' => 'button']
            );
        }?>

        <?php if (in_array($application['status_id'], [1, 4, 8])) {
            echo $this->Html->link(
                'Delete',
                [
                    'controller' => 'Applications',
                    'action' => 'delete',
                    'id' => $application['id'],
                ],
                ['class' => 'button']
            );
        } ?>
    </div>
<?php } ?>
