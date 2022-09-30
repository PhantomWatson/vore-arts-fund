<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\Model\Entity\User $user
 * @var \App\View\AppView $this
 * @var string $note
 * @var string $url
 */
?>
<p>
    <?= $user->name ?>,
</p>

<p>
    Before we can accept your application for funding for <strong><?= $application->title ?></strong>, we need it to be
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
