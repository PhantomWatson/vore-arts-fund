<?php
use Cake\ORM\TableRegistry;

$applications = TableRegistry::getTableLocator()->get('Applications')->find()->all()->toArray();
$statuses = TableRegistry::getTableLocator()->get('Statuses')->find()->all()->toArray();
?>
<div class='pb-2 mt-4 mb-2 border-bottom'>
    <h1>Admin</h1>
</div>
<p><?= $this->Html->link('Applications', '/admin/applications', array('class' => 'button')); ?></p>
<p><?= $this->Html->link('Funding Cycles', '/admin/funding-cycles', array('class' => 'button')); ?></p>
