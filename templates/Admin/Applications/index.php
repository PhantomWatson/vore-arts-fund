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

<div class='pb-2 mt-4 mb-2 border-bottom'>
    <h1>Applications</h1>
</div>

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
        <?php foreach ($applications as $application) { ?>
            <tr>
                <td><?php echo $application['title'] ?></td>
                <td><?php echo $statuses[$application['status_id']]['name'] ?></td>
                <td><?php echo $this->Html->link(
                    'View',
                    '/admin/applications/review//' . $application['id'],
                    ['class' => 'button']
                ); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
