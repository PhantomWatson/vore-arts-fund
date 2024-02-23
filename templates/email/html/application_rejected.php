<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\FundingCycle|null $fundingCycle
 * @var \App\View\AppView $this
 * @var string $note
 * @var string $userName
 */
?>
<p>
    <?= $userName ?>,
</p>

<p>
    Thanks for applying to the Vore Arts Fund! We appreciate the work that you put into your application, your interest in our mission to support the local arts, and the work that you do to contribute to our community. However, your application for funding for <strong><?= $project->title ?></strong> was not accepted. This is usually due to a project or an applicant not meeting the Vore Arts Fund's eligibility requirements. Here's what the reviewer who processed your application said:
</p>

<blockquote>
    <?= nl2br($note) ?>
</blockquote>

<p>
    You're welcome to submit another application on <a href="https://VoreArtsFund.org">VoreArtsFund.org</a>, and if it's
    eligible, submitted, and accepted before the application deadline, it will be voted on by the community to determine
    if it will be funded.
    <?php if ($fundingCycle): ?>
        The deadline to apply for funding for the <?= $fundingCycle->name ?> funding cycle is
        <?= $fundingCycle->resubmit_deadline_local->format('F j, Y') ?>.
    <?php else: ?>
        Check the Vore Arts Fund website for information about upcoming funding cycles and their deadlines.
    <?php endif; ?>
</p>
