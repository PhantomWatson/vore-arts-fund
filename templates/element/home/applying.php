<?php
/**
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var bool $fundingCycleIsCurrent
 */
?>

<div class="row">
    <div class="col">
        <section class="card">
            <div class="card-body">
                <h1>
                    <?php if ($fundingCycle): ?>
                        <?= $fundingCycle->name ?> Funding Cycle
                    <?php else: ?>
                        Applications not currently being accepted
                    <?php endif; ?>
                </h1>

                <?php if ($fundingCycle): ?>
                    <?php if ($fundingCycleIsCurrent): ?>
                        <p>
                            <?= $this->Html->link(
                                'Apply for funding',
                                [
                                    'controller' => 'Projects',
                                    'action' => 'apply',
                                ],
                                ['class' => 'btn btn-primary']
                            ) ?>
                        </p>
                    <?php endif; ?>

                    <?= $this->element('FundingCycles/info_table') ?>

                    <p>
                        Visit the
                        <?= $this->Html->link(
                            'Funding Cycles',
                            [
                                'controller' => 'FundingCycles',
                                'action' => 'index',
                            ]
                        ) ?>
                        page for more information about upcoming opportunities to apply for funding.
                    </p>
                <?php else: ?>
                    <p>
                        We're not currently accepting applications, but please check back later for opportunities to apply
                        for funding or to cast your votes in an upcoming funding cycle.
                    </p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
