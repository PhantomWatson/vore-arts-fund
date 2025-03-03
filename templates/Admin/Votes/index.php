<?php
/**
 * @var \App\View\AppView $this
 * @var Project[] $projects
 * @var \App\Model\Entity\FundingCycle|null $fundingCycle
 * @var \App\Model\Entity\FundingCycle[] $fundingCycles
 */

use App\Model\Entity\Project;

$fundingAvailable = $fundingCycle->funding_available;

function getToAward(Project $project, $fundingAvailable)
{
    // Nothing can be awarded to projects that receive no votes
    $receivedVotes = $project->voting_score != null;
    if (!$receivedVotes) {
        return 'N/A';
    }

    // Check if we're financially able to fund this project
    $canFundFull = $fundingAvailable && $project->amount_requested <= $fundingAvailable;
    $canFundPartial = $fundingAvailable && $project->accept_partial_payout;
    $fundable = $canFundFull || $canFundPartial;
    if (!$fundable) {
        return 'Unable to fund';
    }

    // Determine the fundable amount
    $amountToAward = $canFundFull ? $project->amount_requested : $fundingAvailable;
    $amountDisplayed = '$' . number_format($amountToAward);
    if (!$canFundFull) {
        $amountDisplayed .= ' (partial payout)';
    }

    // Mark this project as having been funded
    if ($project->isDisbursed()) {
        return '<span class="voting-results__award voting-results__award--awarded">'
            . '<i class="fa-solid fa-circle-check"></i> '
            . $amountDisplayed . ' awarded</span>';
    }

    // Mark this project as needing to be funded
    $url = \Cake\Routing\Router::url([
        'prefix' => 'Admin',
        'controller' => 'Projects',
        'action' => 'review',
        'id' => $project->id,
        '?' => [
            'amountAwarded' => $amountToAward
        ],
    ]);
    return '<a href="' . $url . '"><span class="voting-results__award voting-results__award--to-award">'
        . '<i class="fa-solid fa-circle-exclamation"></i> '
        . $amountDisplayed . ' to award</span></a>';
}

?>

<?= $this->element('funding_cycle_selector', [
    'url' => [
        'prefix' => 'Admin',
        'controller' => 'Votes',
        'action' => 'index',
    ],
    'fundingCycleId' => $fundingCycle->id ?? null,
]) ?>

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
                <tr>
                    <td>
                        <?= $project->voting_score == null ? '' : $i + 1 ?>
                        <br />
                        <span class="voting-results__score">
                            <?= $project->voting_score === null ? 'No votes' : ('Score: ' . $project->voting_score) ?>
                            <br />
                            <?= number_format(count($project->votes)) . __n(' vote', ' votes', count($project->votes)) ?>
                        </span>
                    </td>
                    <td>
                        <?= $project->title ?>
                        <br />
                        <?= $project->amount_requested_formatted ?>
                    </td>
                    <td>
                        <?= getToAward($project, $fundingAvailable); ?>
                    </td>
                </tr>
                <?php
                // Adjust funding remaining after this project is funded
                if ($project->voting_score != null) {
                    if ($project->amount_requested <= $fundingAvailable) {
                        $fundingAvailable -= $project->amount_requested;
                    } elseif ($fundingAvailable && $project->accept_partial_payout) {
                        $fundingAvailable = 0;
                    }
                }
                ?>
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
