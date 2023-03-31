<?php
/**
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 */
?>

<p>
    <?= $this->Html->link(
        'Back to Funding Cycles',
        [
            'controller' => 'FundingCycles',
            'action' => 'index',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>

<?= $this->element('FundingCycles/info_table', compact('fundingCycle')) ?>
