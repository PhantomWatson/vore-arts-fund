<?php
/**
 * @var \App\Model\Entity\Project[] $projects
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
