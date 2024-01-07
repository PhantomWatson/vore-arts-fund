<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project[] $projects
 * @var \App\Model\Entity\FundingCycle|null $fundingCycle
 * @var \App\Model\Entity\FundingCycle[] $fundingCycles
 */
$fundingAvailable = $fundingCycle->funding_available;
?>

<?= $this->element('funding_cycle_selector', ['url' => [
    'prefix' => 'Admin',
    'controller' => 'Votes',
    'action' => 'index',
]]) ?>

<?php if ($fundingCycle): ?>
    <p>
        Available funds in this cycle: <?= $fundingCycle->funding_available_formatted ?>
    </p>
<?php endif; ?>

<?php if ($projects): ?>
    <table class="table">
        <thead>
            <tr>
                <th>
                    Rank
                </th>
                <th>
                    Project
                </th>
                <th>
                    Total score
                </th>
                <th>
                    Funding requested
                </th>
                <th>
                    To Award
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $i => $project): ?>
                <?php
                    if ($project->voting_score == null) {
                        $toAward = 'N/A';
                    } elseif ($project->amount_requested <= $fundingAvailable) {
                        $toAward = '$' . number_format($project->amount_requested);
                        $fundingAvailable -= $project->amount_requested;
                    } elseif ($fundingAvailable && $project->accept_partial_payout) {
                        $toAward = '$' . number_format($fundingAvailable) . ' (partial payout)';
                        $fundingAvailable = 0;
                    } else {
                        $toAward = 'Unable to fund';
                    }
                ?>
                <tr>
                    <td>
                        <?= $project->voting_score == null ? '' : $i + 1 ?>
                    </td>
                    <td>
                        <?= $project->title ?>
                    </td>
                    <td>
                        <?= $project->voting_score === null ? 'No votes' : $project->voting_score ?>
                    </td>
                    <td>
                        <?= $project->amount_requested_formatted ?>
                    </td>
                    <td>
                        <?= $toAward ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>
        Total to award: $<?= number_format($fundingCycle->funding_available - $fundingAvailable) ?>
    </p>
<?php else: ?>
    <p>
        No projects found in this funding cycle
    </p>
<?php endif; ?>
