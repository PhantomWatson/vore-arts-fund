<?php
/**
 * @var \App\Model\Entity\FundingCycle[] $fundingCycles
 * @var string $title
 */
?>

<?php if ($fundingCycles->count()): ?>
    <p>
        Soon after each funding cycle's voting deadline, funding is disbursed to the applicants chosen by the voting
        public.
    </p>
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
