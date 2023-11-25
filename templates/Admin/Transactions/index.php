<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Transaction> $transactions
 */
?>
<?= $this->Html->link('Add transaction', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>

<?= $this->element('pagination') ?>

<table class="table">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('created', 'Date') ?></th>
            <th><?= $this->Paginator->sort('type') ?></th>
            <th><?= $this->Paginator->sort('amount') ?></th>
            <th><?= $this->Paginator->sort('projects_id', 'Project') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?= h($transaction->created) ?></td>
                <td><?= \App\Model\Entity\Transaction::getTypeName($transaction->type) ?></td>
                <td><?= $transaction->dollar_amount_formatted ?></td>
                <td>
                    <?=
                        $transaction->has('projects')
                            ? $this->Html->link(
                                $transaction->projects->title,
                                ['controller' => 'Projects', 'action' => 'view', $transaction->projects->id]
                            )
                            : ''
                    ?>
                </td>

                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $transaction->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $transaction->id]) ?>
                    <?php if (isset($_GET['delete'])): ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $transaction->id], ['confirm' => __('Are you sure you want to delete that transaction?')]) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->element('pagination') ?>
