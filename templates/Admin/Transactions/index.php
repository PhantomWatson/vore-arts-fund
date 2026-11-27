<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction[]|\Cake\Collection\CollectionInterface $transactions
 * @var array $_GET
 */
?>
<?= $this->Html->link('Add transaction', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>

<?= $this->element('pagination') ?>

<?= $this->element('Admin/Transactions/table', ['transactions' => $transactions]) ?>

<?= $this->element('pagination') ?>
