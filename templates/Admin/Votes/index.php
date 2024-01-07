<?php
/**
 * @var \App\View\AppView $this
 * @var Project[] $projects
 * @var \App\Model\Entity\FundingCycle|null $fundingCycle
 * @var \App\Model\Entity\FundingCycle[] $fundingCycles
 */

use App\Model\Entity\Project;

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
        <br />
        <?php if ($fundingCycle->vote_end->isPast()): ?>
            Voting ended on
        <?php else: ?>
            <?php if ($fundingCycle->vote_begin->isPast()): ?>
                Voting is underway
            <?php else: ?>
                Voting will begin on
                <?= $fundingCycle->vote_begin_local->i18nFormat('MMM d, YYYY') ?>
            <?php endif; ?>
            and will end on
        <?php endif; ?>
        <?= $fundingCycle->vote_end_local->i18nFormat('MMM d, YYYY') ?>
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
                    Award
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $i => $project): ?>
                <?php
                    $fundable = false;
                    $isPartial = false;
                    if ($project->voting_score == null) {
                        $toAward = 'N/A';
                    } elseif ($project->amount_requested <= $fundingAvailable) {
                        $toAward = '$' . number_format($project->amount_requested);
                        $fundingAvailable -= $project->amount_requested;
                        $fundable = true;
                    } elseif ($fundingAvailable && $project->accept_partial_payout) {
                        $toAward = '$' . number_format($fundingAvailable);
                        $fundingAvailable = 0;
                        $fundable = true;
                        $isPartial;
                    } else {
                        $toAward = 'Unable to fund';
                    }
                    if ($fundable) {
                        if ($isPartial) {
                            $toAward .= ' (partial payout)';
                        }
                        if ($project->status_id == Project::STATUS_AWARDED) {
                            $toAward = '<span class="voting-results__award voting-results__award--awarded">'
                                . '<i class="fa-solid fa-circle-check"></i> '
                                . $toAward . ' awarded</span>';
                        } else {
                            $toAward = '<span class="voting-results__award voting-results__award--to-award">'
                                . '<i class="fa-solid fa-circle-exclamation"></i> '
                                . $toAward . ' to award</span>';
                        }
                    }
                ?>
                <tr>
                    <td>
                        <?= $project->voting_score == null ? '' : $i + 1 ?>
                        <br />
                        <span class="voting-results__score">
                            <?= $project->voting_score === null ? 'No votes' : ('Score: ' . $project->voting_score) ?>
                        </span>
                    </td>
                    <td>
                        <?= $project->title ?>
                        <br />
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
