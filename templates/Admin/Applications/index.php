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

$applications = TableRegistry::getTableLocator()->get('Applications')->find()->all()->toArray();
$statuses = TableRegistry::getTableLocator()->get('Statuses')->find()->all()->toArray();
?>

<?= $this->title() ?>

<!-- list applications by status with filters -->
<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($applications as $application): ?>
            <tr>
                <td><?= $application['title'] ?></td>
                <td><?= $statuses[$application['status_id']]['name'] ?></td>
                <td><?= $this->Html->link(
                    'View',
                    [
                        'prefix' => 'admin',
                        'controller' => 'Applications',
                        'action' => 'review',
                        'id' => $application['id'],
                    ],
                    ['class' => 'btn btn-secondary']
                ) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
