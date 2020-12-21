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

use App\Model\Entity\Status;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

$this->disableAutoLayout();

$cakeDescription = 'CakePHP: the rapid development PHP framework';
$fundingCycles = TableRegistry::getTableLocator()->get('FundingCycles')->find()->all()->toArray();
?>
<!DOCTYPE html>
<html>

<head>
    <?= $this->element('head'); ?>
    <title>
        <?= $cakeDescription ?>
    </title>
</head>

<body class="home">
    <?= $this->element('navbar'); ?>
    <div class="container">

        <div class='pb-2 mt-4 mb-2 border-bottom'>
            <h1>Funding Cycles</h1>
        </div>

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
                <?php foreach ($fundingCycles as $fundingCycle) { ?>
                    <tr>
                        <td><?php echo $fundingCycle['application_begin']->i18nFormat('MM/dd/yyyy H:mm') . ' to ' . $fundingCycle['application_end']->i18nFormat('MM/dd/yyyy H:mm'); ?></td>
                        <td><?php echo $fundingCycle['vote_begin']->i18nFormat('MM/dd/yyyy H:mm') . ' to ' . $fundingCycle['vote_end']->i18nFormat('MM/dd/yyyy H:mm'); ?></td>
                        <td>$<?php echo $fundingCycle['funding_available']; ?></td>
                        <td><?php echo $this->Html->link("Edit", '/admin/funding-cycles/edit//' . $fundingCycle['id'], array('class' => 'button')); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>

</html>