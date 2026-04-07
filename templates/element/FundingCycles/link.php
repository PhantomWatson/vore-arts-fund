<?php
/**
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var ?string $append
 * @var \App\View\AppView $this
 */
?>
<?= $this->Html->link(
    $fundingCycle->name . ($append ?? ' funding cycle'),
    [
        'prefix' => false,
        'controller' => 'FundingCycles',
        'action' => 'view',
        'id' => $fundingCycle->id,
    ]
) ?>
