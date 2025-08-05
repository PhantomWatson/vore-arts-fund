<?php
/**
 * @var \App\Model\Entity\FundingCycle[] $fundingCycles
 * @var \App\View\AppView $this
 * @var string[] $cycleCategories
 */
?>

<?php if (empty($fundingCycles)): ?>
    <div class="row">
        <div class="col">
            <section class="card">
                <div class="card-body">
                    <h1>
                        Check back later for funding opportunities
                    </h1>
                    <p>
                        We're not currently accepting applications, but please check back later for opportunities to apply
                        for funding or to cast your votes in an upcoming funding cycle.
                    </p>
                </div>
            </section>
        </div>
    </div>
<?php endif; ?>

<?php foreach ($fundingCycles as $cycleCategory => $fundingCycle): ?>
    <?php
        // Avoid showing upcoming/current voting period if there are no projects to vote on
        if ($cycleCategory === 'nextVoting' || $cycleCategory === 'currentVoting') {
            $projectSummary = $fundingCycle->getProjectsSummary();
            if (!$projectSummary || $projectSummary['accepted'] === 0) {
                continue;
            }
        }
    ?>
    <div class="row">
        <div class="col">
            <section class="card">
                <div class="card-body">
                    <h1>
                        <?= $cycleCategories[$cycleCategory] ?>
                    </h1>
                    <h2>
                        <?= $fundingCycle->name ?> Funding Cycle
                    </h2>
                    <?= $this->element('FundingCycles/info_table', compact('fundingCycle')) ?>
                </div>
            </section>
        </div>
    </div>
<?php endforeach; ?>
