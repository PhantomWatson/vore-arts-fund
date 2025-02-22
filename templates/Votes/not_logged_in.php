<?php
/**
 * @var \App\Model\Entity\FundingCycle|null $cycle
 * @var \App\Model\Entity\FundingCycle|null $nextCycle
 * @var \App\View\AppView $this
 * @var array $toLoad
 * @var bool $canVote
 * @var bool $hasVoted
 * @var bool $isLoggedIn
 * @var bool $isVerified
 * @var bool $showUpcoming
 * @var bool $fundingCycleHasProjects
 */
?>

<?php if ($fundingCycleHasProjects): ?>
    <p>
        Please
        <?= $this->Html->link(
            'log in',
            \App\Application::LOGIN_URL
        ) ?>
        to vote.
    </p>
<?php else: ?>
    <?= $this->element('Votes/no_projects') ?>
<?php endif; ?>
