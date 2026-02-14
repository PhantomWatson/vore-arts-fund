<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\FundingCycle|null $currentApplyingFundingCycle
 * @var \App\View\AppView $this
 * @var string $userName
 * @var string $reapplyUrl
 */

$reapplyLink = "<a href=\"$reapplyUrl\">$reapplyUrl</a>";
?>
<p>
    <?= $userName ?>,
</p>

<p>
    Thanks for applying to the Vore Arts Fund! We appreciate the work that you put into your application, your interest in our mission to support the local arts, and the work that you do to contribute to our community. However, we're sorry to say that due to budgetary constraints we won't be able to provide funding for <strong><?= $project->title ?></strong> in this funding cycle.
</p>

<p>
    We regret that we were unable to support you this time, but we sincerely hope that you reapply.
    <?php if ($currentApplyingFundingCycle): ?>
        To make that easier for you, here's a link to resubmit your application with any changes you'd like to make:
        <?= $reapplyLink ?>.
        The deadline to apply for funding in the <?= $currentApplyingFundingCycle->name ?> funding cycle is <?= $currentApplyingFundingCycle->application_end_local->format('F j, Y') ?>.
    <?php else: ?>
        The Vore Arts Fund is not currently accepting applications, but check <a href="https://VoreArtsFund.org">https://VoreArtsFund.org</a> for information about upcoming application periods and their deadlines. When the next application period opens, you'll be able to use this link to resubmit your application with any changes you'd like to make: <?= $reapplyLink ?>.
    <?php endif; ?>
</p>
