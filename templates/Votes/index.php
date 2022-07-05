<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Application[]|\Cake\ORM\ResultSet $applications
 * @var \App\Model\Entity\FundingCycle|null $cycle
 * @var \App\Model\Entity\FundingCycle|null $nextCycle
 */
$bundlePathBase = \Cake\Core\Configure::read('debug')
    ? 'http://localhost:8081'
    : '/vote-app/dist/index.js';

?>

<?php if ($cycle): ?>
    <?php if ($applications->isEmpty()): ?>
        <div class="alert alert-info">
            Unfortunately, there are no applications available to vote on in this funding cycle.
            <?php if ($nextCycle): ?>
                Voting for the next funding cycle will begin on
                <strong><?= $nextCycle->vote_end->format('F j, Y') ?></strong>
            <?php else: ?>
                Please check back later for information about the next opportunity to vote.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
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
    <?php endif; ?>
<?php else: ?>
    <p class="alert alert-info">
        <?php if ($nextCycle): ?>
            Voting for the <?= $nextCycle->name ?> applicants begins on
            <strong><?= $nextCycle->vote_begin->format('F j, Y') ?></strong>. See you then!
        <?php else: ?>
            Check back later for information about when voting will begin for the next funding cycle.
        <?php endif; ?>
    </p>
<?php endif; ?>

<div id="voting-root"></div>
<script type="module" src="<?= $bundlePathBase ?>/bundle.js"></script>
