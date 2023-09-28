<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var string $note
 * @var string $url
 * @var string $userName
 */
?>
<p>
    <?= $userName ?>,
</p>

<p>
    Before we can accept your application for funding for <strong><?= $project->title ?></strong>, we need it to be
    revised. Here are the details:
</p>

<blockquote>
    <?= nl2br($note) ?>
</blockquote>

<p>
    The deadline for finalizing your application is <?= $fundingCycle->resubmit_deadline->format('F j, Y') ?>,
    after which you won't be able to update it. If it hasn't been revised and accepted by that date, then it won't be
    eligible for funding in this funding cycle.
</p>

<p>
    To revise your application, go to <a href="<?= $url ?>"><?= $url ?></a>.
</p>
