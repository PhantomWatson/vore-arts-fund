<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var int $amount
 * @var string $myProjectsUrl
 * @var string $supportEmail
 * @var string $userName
 */
?>
Congratulations, <?= $userName ?>! We've tallied the votes for the <?= $fundingCycle->name ?> funding cycle, and we're thrilled to tell you that the community ranked your application for funding for <?= $project->title ?> high enough that it was selected to receive a $<?= number_format($amount) ?> loan!

This means that we'll be mailing your loan check out shortly, made out to you and sent to the address you gave us. It might take a little while for it to reach you, but if you don't receive it by two weeks from now, please email <?= $supportEmail ?>.

SO WHAT COMES NEXT? We'd like for you to submit reports to let us know how your project is going,
and when your project is complete. These will be shown to the public, and it's a great way to promote your work to
the community, inform people of upcoming events that your project will be involved with, celebrate your successes,
and be honest about your difficulties. While these reports can be weekly, monthly, or just once your project is
complete, it's MANDATORY that we hear from you at least once per year and that you let us know when
your project is over. Otherwise, our 501(c)(3) rules say we have to disqualify you from any future funding. :(

When you're ready, visit <?= $myProjectsUrl ?> to submit a report. And don't worry; we'll send you a reminder email about this if we haven't heard from you in a while.

Good luck! We'd appreciate it if you would tell folks that you received support from the Vore Arts Fund and that people can get more information about it on https://VoreArtsFund.org. Hope to hear about the success of your project soon!
