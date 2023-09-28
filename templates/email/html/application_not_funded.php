<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\FundingCycle|null $fundingCycle
 * @var \App\View\AppView $this
 * @var string $userName
 */
?>
<p>
    <?= $userName ?>,
</p>

<p>
    We hate to deliver bad news, but we won't be able to provide funding for <strong><?= $project->title ?></strong>
    in this funding cycle. When this happens, it's due to us running out of budgeted money after tallying the votes
    and funding the highest-ranked applications. But hey, you can try again in the next funding cycle!
</p>

<p>
    <?php if ($fundingCycle): ?>
        The deadline to apply for funding on <a href="https://VoreArtsFund.org">VoreArtsFund.org</a> for the
        <?= $fundingCycle->name ?> funding cycle is <?= $fundingCycle->resubmit_deadline->format('F j, Y') ?>.
    <?php else: ?>
        Check <a href="https://VoreArtsFund.org">VoreArtsFund.org</a> for information about upcoming funding cycles and
        their deadlines.
    <?php endif; ?>
</p>
