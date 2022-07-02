<?php
/**
 * @var \App\Model\Entity\FundingCycle[] $fundingCycles
 * @var string $title
 */
?>

<?php if ($fundingCycles): ?>
    <p>
        Recipients are decided and funding is disbursed shortly after each funding cycle's voting deadline.
    </p>
    <?php foreach ($fundingCycles as $fundingCycle): ?>
        <section>
            <h2>
                <?= $fundingCycle->name ?>
            </h2>
            <?= $this->element('FundingCycles/info_table', compact('fundingCycle')) ?>
        </section>
    <?php endforeach; ?>
<?php else: ?>
    <?= $this->element('no_funding_cycle') ?>
<?php endif; ?>
