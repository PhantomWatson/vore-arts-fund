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
?>

<?php if ($cycle): ?>
    <script>
        window.fundingCycleId = <?= json_encode($cycle->id) ?>;
    </script>
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
            <strong><?= $cycle->vote_end_local->format('F j, Y') ?></strong>.
        </p>
    </div>

    <?php $this->Html->script('/viewerjs/viewer.min.js', ['block' => 'script']); ?>
    <?php $this->Html->css('/viewerjs/viewer.min.css', ['block' => true]); ?>
    <div id="root"></div>
    <?= $this->element('load_app_files', ['dir' => 'vote-app']) ?>
<?php endif; ?>
