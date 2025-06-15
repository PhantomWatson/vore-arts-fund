<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project[] $projects
 */
?>

<?php if (empty($projects)): ?>
    <p>
        You have not yet received a loan from the Vore Arts Fund.
    </p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Project</th>
                <th>Awarded Date</th>
                <th>Loan Amount</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td>
                        <?= $this->Html->link(h($project->title), [
                            'controller' => 'Loans',
                            'action' => 'view',
                            'id' => $project->id,
                        ]) ?>
                    </td>
                    <td>
                        <?= $project->loan_awarded_date?->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y') ?>
                    </td>
                    <td>
                        <?= $project->amount_awarded_formatted_cents ?>
                    </td>
                    <td>
                        <?php
                            // Convert repaid amount to dollars
                            $balance = $project->amount_awarded - ($project->getTotalRepaid() / 100);
                        ?>
                        <?php if ($balance < 0): ?>
                            $0 <span class="text-success">
                                (fully paid + extra
                                <?= '$' . number_format(-$balance, 2) ?>
                                donation)
                            </span>
                        <?php elseif ($balance == 0): ?>
                            $0 <span class="text-success">(fully paid)</span>
                        <?php else: ?>
                            $<?= number_format($balance, 2) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
