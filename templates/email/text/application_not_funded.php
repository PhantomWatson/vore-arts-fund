<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\FundingCycle|null $fundingCycle
 * @var \App\View\AppView $this
 * @var string $userName
 */
?>
<?= $userName ?>,

We hate to deliver bad news, but we won't be able to provide funding for <?= $application->title ?> in this funding cycle. When this happens, it's due to us running out of budgeted money after tallying the votes and funding the highest-ranked applications. But hey, you can try again in the next funding cycle!

<?php if ($fundingCycle): ?>
The deadline to apply for funding on https://VoreArtsFund.org for the <?= $fundingCycle->name ?> funding cycle is <?= $fundingCycle->resubmit_deadline->format('F j, Y') ?>.
<?php else: ?>
Check https://VoreArtsFund.org for information about upcoming funding cycles and their deadlines.
<?php endif; ?>
