<?php
/**
 * @var \App\View\AppView $this
 */
?>

<p class="alert alert-info">
    Sorry, but applications are not being accepted at the moment. Please check back later, or visit the
    <?= $this->Html->link(
        'Funding Cycles',
        [
            'prefix' => false,
            'controller' => 'FundingCycles',
            'action' => 'index',
        ]
    ) ?> page for information about upcoming application periods.
</p>
