<?php
/**
 * @var \App\Model\Entity\FundingCycle[]|\Cake\Collection\CollectionInterface $fundingCycles
 * @var string $title
 * @var \App\View\AppView $this
 */
?>

<?php if ($fundingCycles->count()): ?>
    <?php foreach ($fundingCycles as $fundingCycle): ?>
        <section class="funding-cycles">
            <h2>
                <?= $fundingCycle->name ?>
            </h2>
            <?= $this->element('FundingCycles/info_table', compact('fundingCycle')) ?>
        </section>
    <?php endforeach; ?>
<?php else: ?>
    <?= $this->element('no_funding_cycle') ?>
<?php endif; ?>
