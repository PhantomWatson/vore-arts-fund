<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 */
?>
Congratulations, <?= $user->name ?>! Your application for funding for <?= $application->title ?> was accepted. However,
your application needs to be voted on by the public before we can award funding.

So what comes next? On <?= $fundingCycle->vote_begin->format('F j, Y') ?>, voting will begin for the applications
in this funding cycle. Then on <?= $fundingCycle->vote_end->format('F j, Y') ?>, the
voting period will end, and we'll email you soon afterward to let you know whether or not you'll be awarded funding.

Good luck, and be sure to tell your supporters to visit https://VoreArtsFund.org and vote for you!