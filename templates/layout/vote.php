<?php
/**
 * @var \App\View\AppView $this
 * @var bool $showUpcoming
 * @var \App\Model\Entity\FundingCycle|null $nextCycle
 */
$this->extend('default');
?>

<?= $this->element('Votes/intro') ?>

<?= $this->fetch('content') ?>

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
