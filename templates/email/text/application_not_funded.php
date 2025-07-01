<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\FundingCycle|null $fundingCycle
 * @var \App\View\AppView $this
 * @var string $userName
 */
?>
<?= $userName ?>,

Thanks for applying to the Vore Arts Fund! We appreciate the work that you put into your application, your interest in our mission to support the local arts, and the work that you do to contribute to our community. However, we're sorry to say that due to budgetary constraints we won't be able to provide funding for <?= $project->title ?> in this funding cycle. But hey, you're invited to reapply!

<?php if ($fundingCycle): ?>
The deadline to apply for funding on https://VoreArtsFund.org for the <?= $fundingCycle->name ?> funding cycle is <?= $fundingCycle->resubmit_deadline_local->format('F j, Y') ?>.
<?php else: ?>
Check https://VoreArtsFund.org for information about upcoming funding cycles and their deadlines.
<?php endif; ?>
