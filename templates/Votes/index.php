<?php
/**
 * @var \App\Model\Entity\Project[] $projects
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
?>

<?php if ($hasVoted): ?>
    <p class="alert alert-success">
        Thank you for voting in this funding cycle!
    </p>
<?php endif; ?>

<?php if ($cycle): ?>
    <?php if (!$isLoggedIn): ?>
        <p class="alert alert-warning">
            You must
            <?= $this->Html->link(
                'log in',
                [
                    'controller' => 'Users',
                    'action' => 'login',
                ]
            ) ?>
            to vote.
        </p>
    <?php elseif (!$isVerified): ?>
        <p class="alert alert-warning">
            Before you can vote, you must first
            <?= $this->Html->link('verify your phone number', ['controller' => 'Users', 'action' => 'verify']) ?>.
        </p>
    <?php endif; ?>

    <script>
        window.fundingCycleId = <?= json_encode($cycle->id) ?>;
    </script>
<?php endif; ?>

<?php if (!$projects): ?>
    <p>
        Unfortunately, there are no applications to vote on in this funding cycle.
    </p>
<?php endif; ?>

<?php if ($canVote): ?>
    <div>
        <p>
            Voting is underway for the applicants in this funding cycle! Here are the steps:
        </p>
        <ol>
            <li>
                <strong>Select</strong> all of the applications that you think should be funded.
            </li>
            <li>
                <strong>Rank</strong> those applications from highest-priority to lowest-priority.
            </li>
            <li>
                <strong>Submit</strong> your vote!
            </li>
        </ol>
        <p>
            The deadline to cast your votes is
            <strong><?= $cycle->vote_end->format('F j, Y') ?></strong>.
        </p>
    </div>

    <?php $this->Html->script('/viewerjs/viewer.min.js', ['block' => 'script']); ?>
    <?php $this->Html->css('/viewerjs/viewer.min.css', ['block' => true]); ?>
    <div id="root"></div>
    <?= $this->element('load_app_files', ['dir' => 'vote-app']) ?>
<?php endif; ?>

<?php if ($showUpcoming): ?>
    <p>
        <?php if ($nextCycle): ?>
            Voting for the <?= $nextCycle->name ?> applicants begins on
            <strong><?= $nextCycle->vote_begin_local->format('F j, Y') ?></strong>. See you then!
        <?php else: ?>
            Check back later for information about when voting will begin for the next funding cycle.
        <?php endif; ?>
    </p>
<?php endif; ?>
