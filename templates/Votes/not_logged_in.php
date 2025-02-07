<?php
/**
 * @var \App\Model\Entity\FundingCycle[] $cyclesCurrentlyVoting
 * @var \App\Model\Entity\FundingCycle|null $cycle
 * @var \App\Model\Entity\FundingCycle|null $nextCycle
 * @var \App\View\AppView $this
 * @var array $toLoad
 * @var bool $canVote
 * @var bool $hasVoted
 * @var bool $isLoggedIn
 * @var bool $isVerified
 * @var bool $showUpcoming
 */
$count = count($projects);
?>

<?php if ($projects): ?>
    <p>
        There
        <?= __n('is currently', 'are currently', $count) ?>
        <?= $this->Html->link(
            __n('one project', "$count projects", $count),
            [
                'prefix' => false,
                'controller' => 'Projects',
                'action' => 'index',
            ]
        ) ?>
        to vote on!
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
