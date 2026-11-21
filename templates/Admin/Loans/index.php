<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet<Project> $projects
 * @var array<int, int> $balances
 * @var int $totalAwarded
 */

use App\Model\Entity\Project;

$getRowClass = function (Project $project, int $balance) {
    // Paid off
    if ($balance <= 0) {
        return 'table-success';
    }

    // Too recent to show scary colors about
    if ($project->loan_awarded_date->wasWithinLast('1 year')) {
        return 'table-info';
    }

    // Not paid at all
    if ($balance === $project->amount_awarded) {
        return 'table-danger';
    }

    // Partially paid
    return 'table-warning';
};
?>

<table class="table">
    <tbody>
        <tr>
            <th>Color key:</th>
            <td class="table-info text-center">Recent</td>
            <td class="table-success text-center">Fully Paid</td>
            <td class="table-warning text-center">Partially Paid</td>
            <td class="table-danger text-center">Not Paid</td>
        </tr>
    </tbody>
</table>

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
            <tr class="<?= $getRowClass($project, $balance) ?>">
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
                            (donated extra <?= '$' . number_format(-$balance / 100, 2) ?>)
                        </span>
                    <?php elseif ($balance == 0): ?>
                        $0
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
