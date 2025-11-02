<?php
/**
 * @var string $projectTitle
 * @var string $userName
 * @var string $reportUrl
 * @var string $supportEmail
 * @var string $repaymentUrl
 */
?>

<p>
    <?= $userName ?>,
</p>

<p>
    How have things been going with your project, <strong><?= h($projectTitle) ?></strong>? We hope that it's been going well and that you're enjoying the process of bringing your creative vision to life. This is reminder that we would love to hear from you about your progress, challenges, and any exciting developments that have occurred since you received your Vore Arts Fund loan.
</p>

<p>
    These reports are an important part of our community, as they help us understand how the funds are being used and the impact they are having on your project. They also allow us to share your journey with others who may be interested in supporting similar initiatives in the future. And don't forget that they're a great way to promote your work to the community and inform people of upcoming events that your project will be involved with!
</p>

<p>
    To submit a report for this project, visit <a href="<?= $reportUrl ?>"><?= $reportUrl ?></a>. While regular updates about your project are great, your only requirement is to submit one final report upon the completion of your project and at least one report per year. We'll send you reminders if you're approaching a deadline.
</p>

<?php if ($repaymentUrl): ?>
    <p>
        And remember, once you're able to make a payment on your loan, head over to <a href="<?= $repaymentUrl ?>"><?= $repaymentUrl ?></a>.
    </p>
<?php endif; ?>

<p>
    If you have any questions or need assistance, please don't hesitate to reach out to us at <a href="mailto:<?= $supportEmail ?>"><?= $supportEmail ?></a>.
</p>
