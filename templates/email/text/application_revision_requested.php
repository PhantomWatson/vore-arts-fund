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
<?= $userName ?>,

Before we can accept your application for funding for <?= $project->title ?>, we need it to be revised. Here are the details:

<?= nl2br($note) ?>


The deadline for finalizing your application is <?= $fundingCycle->resubmit_deadline_local->format('F j, Y') ?>, after which you won't be able to update it. If it hasn't been revised and accepted by that date, then it won't be eligible for funding in this funding cycle.

To revise your application, go to <?= $url ?>.
