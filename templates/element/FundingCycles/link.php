<?php
/**
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 */
?>
<?= $this->Html->link(
    $fundingCycle->name . ' funding cycle',
    [
        'controller' => 'FundingCycles',
        'action' => 'view',
        'id' => $fundingCycle->id,
    ]
) ?>
