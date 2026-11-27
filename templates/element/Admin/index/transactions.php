<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet<\App\Model\Entity\Report> $recentReports
 * @var \Cake\ORM\ResultSet<\App\Model\Entity\Transaction> $recentTransactions
 */
?>

<div class="card">
    <div class="card-header">
        <h2>
            Transactions
        </h2>
        <ul class="links">
            <li>
                <?= $this->Html->link('View all', [
                    'prefix' => 'Admin',
                    'controller' => 'Transactions',
                    'action' => 'index',
                ]) ?>
            </li>
            <li>
                <?= $this->Html->link('Record a transaction', [
                    'prefix' => 'Admin',
                    'controller' => 'Transactions',
                    'action' => 'add',
                ]) ?>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <h3>
            Recent
        </h3>
        <?= $this->element('Admin/Transactions/table', ['transactions' => $recentTransactions]) ?>
    </div>
</div>
