<?php
/**
 * @var \App\View\AppView $this
 * @var Transaction $transaction
 * @var \Cake\Collection\CollectionInterface|string[] $projects
 * @var array $cycles
 * @var Transaction|null $transaction
 */

use App\Model\Entity\Transaction;

$types = Transaction::getTypes();

$action = $this->getRequest()->getParam('action');

if (isset($transaction->date)) {
    // Zero out seconds so this form can use minute-granularity
    $transaction->date = $transaction->date->subSeconds($transaction->date->second);

    // Convert to string
    $transaction->date = $transaction->date->toIso8601String(); // e.g. 2025-03-07T02:12:00-05:00

    // Remove timezone suffix
    $transaction->date = substr($transaction->date, 0, 19); // e.g. 2025-03-07T02:12:00
}
?>

<div id="root"></div>

<?= $this->element('load_app_files', ['jsType' => 'module']) ?>

<script>
    window.transactionForm = {
        cycles: <?= json_encode($cycles) ?>,
        transactionTypes: <?= json_encode($types) ?>,
        action: <?= json_encode($action) ?>,
        transaction: <?= json_encode($transaction ?? new stdClass()) ?>,
        endpointUrl: <?= json_encode('/api/transactions' . ($action == 'edit' ? '/' . $transaction->id : '')) ?>,
    };
</script>
