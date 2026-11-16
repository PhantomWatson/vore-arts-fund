<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet<\App\Model\Entity\Project> $projects
 */
?>

<table class="table">
    <thead>
        <tr>
            <th>Recipient</th>
            <th>Project</th>
            <th>Awarded Date</th>
            <th>Loan Amount</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($projects as $project): ?>
            <?php
                $balance = $balances[$project->id];
            ?>
            <tr class="<?= $balance <= 0 ? 'table-success' : ($balance === $project->amount_awarded ? 'table-danger' : 'table-warning') ?>">
                <td>
                    <?= $project->user->name ?>
                </td>
                <td>
                    <?= $this->Html->link(
                        "$project->title #$project->id",
                        [
                            'prefix' => 'Admin',
                            'controller' => 'Projects',
                            'action' => 'review',
                            $project->id,
                        ]
                    ) ?>
                </td>
                <td>
                    <?= $project->loan_awarded_date_formatted ?>
                </td>
                <td>
                    <?= $project->amount_awarded_formatted_cents ?>
                </td>
                <td>
                    <?php if ($balance < 0): ?>
                        $0 <span class="text-success">
                            (fully paid + extra
                            <?= '$' . number_format(-$balance / 100, 2) ?>
                            donation)
                        </span>
                    <?php elseif ($balance == 0): ?>
                        $0 <span class="text-success">(fully paid)</span>
                    <?php else: ?>
                        $<?= number_format($balance / 100, 2) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-end">Total:</th>
            <th><?= '$' . number_format($totalAwarded / 100, 2) ?></th>
            <th><?= '$' . number_format(array_sum($balances) / 100, 2) ?></th>
        </tr>
    </tfoot>
</table>
