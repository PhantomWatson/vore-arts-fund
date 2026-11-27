<?php
/**
 * @var \App\View\AppView $this
 * @var array{
 *      takingApplications: \Cake\ORM\ResultSet<\App\Model\Entity\FundingCycle>,
 *      voting: \Cake\ORM\ResultSet<\App\Model\Entity\FundingCycle>,
 *      nextTakingApplications: \App\Model\Entity\FundingCycle|null,
 *      needsAwarding: \Cake\ORM\ResultSet<\App\Model\Entity\FundingCycle>,
 *  } $fundingCycles
 */

$needsAwarding = !$fundingCycles['needsAwarding']->isEmpty();

$nextVotingEndCycle = match (true) {
    !$fundingCycles['voting']->isEmpty() => $fundingCycles['voting']->first(),
    !$fundingCycles['takingApplications']->isEmpty() => $fundingCycles['takingApplications']->first(),
    (bool)$fundingCycles['nextTakingApplications'] => $fundingCycles['nextTakingApplications'],
    default => null,
};

$nextVotingEndDate = $nextVotingEndCycle
    ? $nextVotingEndCycle->vote_end_local->format('F j, Y')
    : null;

$cycleForAwarding = $fundingCycles['needsAwarding']->first();
?>

<div class="card" id="awarding-anchor">
    <div class="card-header">
        <h2>
            Awarding
            <?php if ($needsAwarding): ?>
                <i class="fa-solid fa-circle-exclamation" style="color: red;"></i>
            <?php endif; ?>
        </h2>
        <ul class="links">
            <li>
                <?= $this->Html->link('History', [
                    'prefix' => 'Admin',
                    'controller' => 'Votes',
                    'action' => 'index',
                ]) ?>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <?php if ($cycleForAwarding): ?>
            <p>
                <?= $this->Html->link(
                    'Finalize the winners of the ' . $cycleForAwarding->name . ' funding cycle',
                    [
                        'prefix' => 'Admin',
                        'controller' => 'Votes',
                        'action' => 'index',
                        'id' => $cycleForAwarding->id,
                    ],
                    ['class' => 'btn btn-primary']
                ) ?>
            </p>
        <?php elseif ($nextVotingEndDate): ?>
            <p>
                Voting will conclude on <?= $nextVotingEndDate ?>.
            </p>
        <?php else: ?>
            <p>
                There are no funding cycles that need awarding, and there are no upcoming funding cycles.
            </p>
        <?php endif; ?>
    </div>
</div>
