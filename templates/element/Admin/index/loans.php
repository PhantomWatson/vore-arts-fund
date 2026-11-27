<?php
/**
 * @var \App\View\AppView $this
 * @var array<array{date: \Cake\I18n\DateTime, loansOutstanding: float, loansRepaid: float}> $loanHistory
 * @var \Cake\ORM\ResultSet<\App\Model\Entity\Project> $recentOutstandingLoans
 * @var int $outstandingLoanCount
 */

$moreCount = $outstandingLoanCount - $recentOutstandingLoans->count();
?>

<div class="card">
    <div class="card-header">
        <h2>
            Loans
        </h2>
        <ul class="links">
            <li>
                <?= $this->Html->link('All loan statuses', [
                    'prefix' => 'Admin',
                    'controller' => 'Loans',
                    'action' => 'index',
                ]) ?>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <?php if ($recentOutstandingLoans->isEmpty()): ?>
            <p>
                No outstanding loans
            </p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Recently awarded loan</th>
                        <th>Awarded date</th>
                        <th>Outstanding balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOutstandingLoans as $project): ?>
                        <tr>
                            <td>
                                <?= $this->Html->link(
                                    $project->title,
                                    [
                                        'prefix' => 'Admin',
                                        'controller' => 'Projects',
                                        'action' => 'view',
                                        $project->id,
                                    ]
                                ) ?>
                            </td>
                            <td>
                                <?= $project->loan_awarded_date_formatted ?>
                            </td>
                            <td>
                                <?php
                                    $balance = $project->getLoanBalance();
                                    echo '$' . number_format($project->getLoanBalance());
                                ?>
                                <?php if ($balance === 0): ?>
                                    <span class="badge text-bg-warning">
                                        Zero balance, but not marked as repaid
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($moreCount): ?>
                <p>
                    plus <?= $moreCount ?> more outstanding <?= __n('loan', 'loans', $moreCount) ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
