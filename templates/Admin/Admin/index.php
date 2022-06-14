<?php
/**
 * @var \App\Model\Entity\Application[] $applications
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
<p>
    <?= $this->Html->link(
        'Questions',
        [
            'prefix' => 'Admin',
            'controller' => 'Questions',
            'action' => 'index',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>
