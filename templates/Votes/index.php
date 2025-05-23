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
 */
?>

<?php if ($cycle): ?>
    <script>
        window.fundingCycleId = <?= json_encode($cycle->id) ?>;
    </script>
<?php endif; ?>

<?php if ($canVote): ?>
    <?php $this->Html->script('/viewerjs/viewer.min.js', ['block' => 'script']); ?>
    <?php $this->Html->css('/viewerjs/viewer.min.css', ['block' => true]); ?>
    <div id="root"></div>
    <?= $this->element('load_app_files') ?>
<?php endif; ?>
