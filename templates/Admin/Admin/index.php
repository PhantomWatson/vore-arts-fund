<?php
/**
 * @var \App\Model\Entity\Project[] $projects
 * @var \App\View\AppView $this
 */
?>


<p>
    <?= $this->Html->link(
        'Projects',
        [
            'prefix' => 'Admin',
            'controller' => 'Projects',
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
<p>
    <?= $this->Html->link(
        'Transactions',
        [
            'prefix' => 'Admin',
            'controller' => 'Transactions',
            'action' => 'index',
        ],
        ['class' => 'btn btn-secondary']
    ) ?>
</p>
