<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var string $note
 * @var string $userName
 */
?>
<?= $userName ?>,

We hate to deliver bad news, but your application for funding for <strong><?= $application->title ?></strong> was not accepted. This is usually due to a project or an applicant not meeting the Vore Arts Fund's eligibility requirements. Here's what the reviewer who processed your application said:

<?= nl2br($note) ?>


You're welcome to submit another application on https://VoreArtsFund.org, and if it's eligible, submitted, and accepted before the application deadline, it will be voted on by the community to determine if it will be funded.

<?php if ($fundingCycle): ?>
The deadline to apply for funding for the <?= $fundingCycle->name ?> funding cycle is <?= $fundingCycle->resubmit_deadline->format('F j, Y') ?>.
<?php else: ?>
Check the Vore Arts Fund website for information about upcoming funding cycles and their deadlines.
<?php endif; ?>