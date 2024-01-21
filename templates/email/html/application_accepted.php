<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var string $userName
 */
?>
<p>
    Congratulations, <?= $userName ?>! Your application for funding for <strong><?= $project->title ?></strong>
    was accepted. However, your application needs to be voted on by the public before we can award funding.
</p>

<p>
    So what comes next?
    <?php if ($fundingCycle->vote_begin_local->isPast()): ?>
        Voting is currently underway for applications in this funding cycle, and
    <?php else: ?>
        On <?= $fundingCycle->vote_begin_local->format('F j, Y') ?>, voting will begin for the
        applications in this funding cycle. Then
    <?php endif; ?>
    on <?= $fundingCycle->vote_end_local->format('F j, Y') ?>, the
    voting period will end, and we'll email you soon afterward to let you know whether or not you'll be awarded funding.
</p>

<p>
    Good luck, and be sure to tell your supporters to visit <a href="https://VoreArtsFund.org">VoreArtsFund.org</a> and
    vote for you!
</p>
