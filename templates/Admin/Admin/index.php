<?php
/**
 * @var \App\Model\Entity\Application[] $applications
 * @var \App\Model\Entity\Status[] $status
 */
?>

<?= $this->title() ?>

<p>
    <?= $this->Html->link(
        'Applications',
        [
            'prefix' => 'Admin',
            'controller' => 'Applications',
            'action' => 'index',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>
<p>
    <?= $this->Html->link(
        'Funding Cycles',
        [
            'prefix' => 'Admin',
            'controller' => 'FundingCycles',
            'action' => 'index',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>
