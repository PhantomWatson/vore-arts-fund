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
            'controller' => 'Admin',
            'action' => 'Applications',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>
<p>
    <?= $this->Html->link(
        'Funding Cycles',
        [
            'controller' => 'Admin',
            'action' => 'fundingCycles',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>
