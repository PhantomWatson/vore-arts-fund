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

$fundingCycles = TableRegistry::getTableLocator()->get('FundingCycles')->find()->all()->toArray();
?>

<?= $this->title() ?>

<!-- list applications by status with filters -->
<table>
    <thead>
        <tr>
            <th>Application Dates</th>
            <th>Voting Dates</th>
            <th>Funding Available</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fundingCycles as $fundingCycle): ?>
            <tr>
                <td><?= sprintf(
                    '%s to %s',
                    $fundingCycle['application_begin']->i18nFormat('MM/dd/yyyy H:mm'),
                    $fundingCycle['application_end']->i18nFormat('MM/dd/yyyy H:mm')
                ) ?></td>
                <td><?= sprintf(
                    '%s to %s',
                    $fundingCycle['vote_begin']->i18nFormat('MM/dd/yyyy H:mm'),
                    $fundingCycle['vote_end']->i18nFormat('MM/dd/yyyy H:mm')
                ) ?></td>
                <td>$<?= $fundingCycle['funding_available'] ?></td>
                <td>
                    <?= $this->Html->link(
                        'Edit',
                        [
                            'prefix' => 'admin',
                            'controller' => 'FundingCycles',
                            'action' => 'edit',
                            'id' => $fundingCycle['id'],
                        ],
                        ['class' => 'btn btn-secondary']
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
