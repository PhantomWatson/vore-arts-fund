<?php
/**
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var bool $fundingCycleIsCurrent
 */
?>

<?php if ($fundingCycle): ?>
    <div class="row">
        <div class="col">
            <section class="card">
                <div class="card-body">
                    <h1>
                        <?php if ($fundingCycle): ?>
                            <?= $fundingCycle->name ?> Funding Cycle
                        <?php endif; ?>
                    </h1>

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
                </div>
            </section>
        </div>
    </div>
<?php endif; ?>
