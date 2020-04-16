<?php
use App\Model\Entity\Status;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

$this->layout = false;

$cakeDescription = 'CakePHP: the rapid development PHP framework';
$applications = TableRegistry::getTableLocator()->get('Applications')->find()->all()->toArray();
$statuses = TableRegistry::getTableLocator()->get('Statuses')->find()->all()->toArray();

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
            <h1>Admin</h1>
        </div>
        <p><?= $this->Html->link('Applications', '/admin/applications'); ?></p>
        <p><?= $this->Html->link('Funding Cycles', '/admin/funding-cycles'); ?></p>

    </div>

</body>

</html>