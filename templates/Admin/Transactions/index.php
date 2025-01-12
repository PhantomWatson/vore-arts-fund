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
            <th><?= $this->Paginator->sort('date', 'Date') ?></th>
            <th><?= $this->Paginator->sort('type') ?></th>
            <th><?= $this->Paginator->sort('name') ?></th>
            <th><?= $this->Paginator->sort('amount_gross', 'Amount (net)') ?></th>
            <th><?= $this->Paginator->sort('project_id', 'Project') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?= $transaction->date?->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('M j, Y g:ia') ?></td>
                <td><?= $transaction->type_name ?></td>
                <td><?= $transaction->name ?></td>
                <td>
                    <?= $transaction->dollar_amount_gross_formatted ?>
                    <?php if ($transaction->amount_net && $transaction->amount_net != $transaction->amount_gross): ?>
                        (<?= $transaction->dollar_amount_net_formatted ?>)
                    <?php endif; ?>
                </td>
                <td>
                    <?=
                        $transaction->has('project')
                            ? $this->Html->link(
                                $transaction->project->title,
                                ['controller' => 'Projects', 'action' => 'view', 'id' => $transaction->project->id]
                            )
                            : ''
                    ?>
                </td>

                <td class="actions">
                    <?= $this->Html->link(
                        'View',
                        ['action' => 'view', 'id' => $transaction->id],
                        ['class' => 'btn btn-sm btn-secondary'],
                    ) ?>
                    <?= $this->Html->link(
                        'Edit',
                        ['action' => 'edit', 'id' => $transaction->id],
                        ['class' => 'btn btn-sm btn-secondary'],
                    ) ?>
                    <?php if (isset($_GET['delete'])): ?>
                        <?= $this->Form->postLink(
                            'Delete',
                            ['action' => 'delete', 'id' => $transaction->id],
                            [
                                'confirm' => 'Are you sure you want to delete that transaction?',
                                'class' => 'btn btn-sm btn-danger'
                            ]
                        ) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->element('pagination') ?>
