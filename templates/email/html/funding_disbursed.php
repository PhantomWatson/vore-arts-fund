<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\View\AppView $this
 * @var string $myProjectsUrl
 * @var string $supportEmail
 * @var string $userName
 * @var string $replyUrl
 */
?>

<p>
    <?= $userName ?>,
</p>

<p>
    Thanks for submitting your loan agreement! We're pleased to say that your check to help pay for <strong><?= $project->title ?></strong> is now on its way to you. It might take a few days to reach you, but if you don't receive it by two weeks from now, please email <a href="mailto:<?= $supportEmail ?>"><?= $supportEmail ?></a>.
</p>

<p>
    <strong>So what comes next?</strong> We'd like for you to submit check-in reports to let us know how your project is going, and a final report when your project is complete. These will be shown to the public, and it's a great way to promote your work to the community, inform people of upcoming events that your project will be involved with, celebrate your successes, and be honest about your difficulties. In fact, telling folks about your struggles is a great way to solicit community support!
</p>

<p>
    While these reports can be weekly, monthly, or just once you've finished your project, it's <strong>mandatory</strong> that we hear from you at least once per year and that you let us know when your project is complete. Otherwise, our 501(c)(3) rules handed down by the government say we have to disqualify you from any future funding. :(
</p>

<p>
    When you're ready, visit <a href="<?= $myProjectsUrl ?>"><?= $myProjectsUrl ?></a> to submit a report to let us know how things are going. And don't worry; we'll send you a reminder email about this if we haven't heard from you in a while.
</p>

<p>
    If you have any questions, please visit <a href="<?= $replyUrl ?>">the Messages page for this project</a> to send us a message.
</p>

<p>
    Good luck! We'd appreciate it if you would tell folks that you received support from the Vore Arts Fund and that people can get more information about it on <a href="https://VoreArtsFund.org">VoreArtsFund.org</a>. Hope to hear about the success of your project soon!
</p>