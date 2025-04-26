<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FundingCycle|null $nextCycle
 * @var bool $showUpcoming
 */
$this->extend('default');
?>

<?= $this->element('Votes/intro') ?>

<?= $this->fetch('content') ?>
