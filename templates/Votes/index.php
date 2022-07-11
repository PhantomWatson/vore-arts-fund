<?php
/**
 * @var \App\Model\Entity\Application[] $applications
 * @var \App\Model\Entity\FundingCycle|null $cycle
 * @var \App\Model\Entity\FundingCycle|null $nextCycle
 * @var \App\View\AppView $this
 * @var bool $canVote
 * @var bool $hasVoted
 * @var bool $isLoggedIn
 * @var bool $showUpcoming
 */
$bundlePathBase = \Cake\Core\Configure::read('debug')
    ? 'http://vore.test:8081/vote-app/dist'
    : '/vote-app/dist/index.js';
?>

<?php if ($hasVoted): ?>
    <p class="alert alert-info">
        Thank you for voting in this funding cycle!
    </p>
<?php endif; ?>

<?php if ($cycle): ?>
    <div class="alert alert-info">
        <?php if ($applications): ?>
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
        <?php else: ?>
            <p>
                Unfortunately, there are no applications to vote on in this funding cycle.
            </p>
        <?php endif; ?>
    </div>

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
    <?php endif; ?>
<?php endif; ?>

<?php if ($canVote): ?>
    <div id="voting-root"></div>
    <script type="module" src="<?= $bundlePathBase ?>/bundle.js"></script>
<?php endif; ?>

<?php if ($showUpcoming): ?>
    <p class="alert alert-info">
        <?php if ($nextCycle): ?>
            Voting for the <?= $nextCycle->name ?> applicants begins on
            <strong><?= $nextCycle->vote_begin->format('F j, Y') ?></strong>. See you then!
        <?php else: ?>
            Check back later for information about when voting will begin for the next funding cycle.
        <?php endif; ?>
    </p>
<?php endif; ?>
