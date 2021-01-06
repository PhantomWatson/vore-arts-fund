<?php
use Cake\ORM\TableRegistry;

$applications = TableRegistry::getTableLocator()->get('Applications')->find()->all()->toArray();
$statuses = TableRegistry::getTableLocator()->get('Statuses')->find()->all()->toArray();
?>
<div class="pb-2 mt-4 mb-2 border-bottom">
    <h1>Admin</h1>
</div>
<p>
    <?= $this->Html->link(
        'Applications',
        [
            'controller' => 'Admin',
            'action' => 'Applications',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>
<p>
    <?= $this->Html->link(
        'Funding Cycles',
        [
            'controller' => 'Admin',
            'action' => 'fundingCycles',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>
