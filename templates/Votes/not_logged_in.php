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
 * @var \App\Model\Entity\Project[] $projects
 */
?>

<?php if ($fundingCycleHasProjects): ?>
    <div style="text-align: center;" class="alert alert-warning">
        <p>
            You must be logged in to vote.
        </p>
        <p>
            <?= $this->Html->link(
                'Log in',
                \App\Application::LOGIN_URL,
                ['class' => 'btn btn-primary']
            ) ?>
            or
            <?= $this->Html->link(
                'Create an account',
                \App\Application::REGISTER_URL,
                ['class' => 'btn btn-secondary']
            ) ?>
        </p>
    </div>
<?php else: ?>
    <?= $this->element('Votes/no_projects') ?>
<?php endif; ?>
