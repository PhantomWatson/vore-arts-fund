<?php
/**
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var bool $fundingCycleIsCurrent
 */
?>
<div id="home">
    <section>
        <h1>
            The Vore Arts Fund
        </h1>
        <div class="info">
            <p>
                The Vore Arts Fund is a 501(c)(3) not-for-profit program for supporting the Muncie, Indiana arts
                community by distributing no-contract, no-interest loans to cover the up-front costs of producing
                for-profit art, music, performances, and art education.
            </p>
            <p>
                We're currently fundraising and setting up the program's website, and applications for funding are
                not yet open to the public. Stay tuned for updates.
            </p>
            <p>
                Please email <a href="mailto:info@voreartsfund.org">info@voreartsfund.org</a> if you have any
                questions.
            </p>
        </div>
    </section>

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
                                'controller' => 'Applications',
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
