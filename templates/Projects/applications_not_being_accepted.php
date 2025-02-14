<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FundingCycle|null $nextFundingCycle
 */
?>

<div class="alert alert-info">
    <p>
        Sorry, but applications are not being accepted at the moment.
    </p>

    <?php if ($nextFundingCycle): ?>
        <p>
            The application period for the <?= $nextFundingCycle->name ?> funding cycle
            <strong>will begin on <?= $nextFundingCycle->application_begin_local->format('F j, Y') ?></strong>.
        </p>
    <?php endif; ?>

    <p>
        <?= $this->Html->link(
            'Sign up for our mailing list',
            ['controller' => 'MailingList', 'action' => 'signup'],
        ) ?> or visit the
        <?= $this->Html->link(
            'Funding Cycles',
            [
                'prefix' => false,
                'controller' => 'FundingCycles',
                'action' => 'index',
            ]
        ) ?> page for more information about upcoming application periods.
    </p>
</div>
