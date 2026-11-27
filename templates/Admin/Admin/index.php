<?php
/**
 * @var \Cake\ORM\ResultSet<Project> $applications
 * @var \App\View\AppView $this
 * @var array{
 *     takingApplications: \Cake\ORM\ResultSet<\App\Model\Entity\FundingCycle>,
 *     voting: \Cake\ORM\ResultSet<\App\Model\Entity\FundingCycle>,
 *     nextTakingApplications: \App\Model\Entity\FundingCycle|null,
 *     needsAwarding: \Cake\ORM\ResultSet<\App\Model\Entity\FundingCycle>,
 * } $fundingCycles
 * @var \Cake\ORM\ResultSet<Project> $recentOutstandingLoans
 * @var int $outstandingLoanCount
 * @var \Cake\ORM\ResultSet<\App\Model\Entity\Report> $recentReports
 * @var \App\Model\Entity\Article|null $article
 * @var \Cake\ORM\ResultSet<\App\Model\Entity\Transaction> $recentTransactions
 */

use App\Model\Entity\Project;

$controllers = [
    'Articles' => 'News articles',
    'Transactions' => 'Transactions',
];

$tabs = [
    'Funding Cycles' => 'funding_cycles',
    'Applications' => 'applications',
    'Awarding' => 'awarding',
    'Loans' => 'loans',
    'Reports' => 'reports',
    'Transactions' => 'transactions',
    'Articles' => 'articles',
];

$needsAwarding = !$fundingCycles['needsAwarding']->isEmpty();
$needsReviewing = $applications->some(function (Project $application) {
    return $application->status_id == Project::STATUS_UNDER_REVIEW;
});
$fundingCycleWarning = $fundingCycles['takingApplications']->count() > 1
    || $fundingCycles['voting']->count() > 1
    || $fundingCycles['needsAwarding']->count() > 1;
$fundingCycleAlert = $fundingCycles['takingApplications']->isEmpty()
    || !$fundingCycles['nextTakingApplications'];
?>

<ul class="nav nav-pills" id="myTab" role="tablist">
    <?php foreach ($tabs as $label => $id): ?>
        <li class="nav-item" role="presentation">
            <button
                class="nav-link <?= $id === array_values($tabs)[0] ? 'active' : '' ?>"
                id="<?= $id ?>-tab"
                data-bs-toggle="tab"
                data-bs-target="#<?= $id ?>-tab-pane"
                type="button" role="tab"
                aria-controls="<?= $id ?>-tab-pane"
                aria-selected="<?= $id === 'funding-cycles' ? 'true' : 'false' ?>"
            >
                <?= $label ?>

                <?php if (
                    ($label == 'Awarding' && $needsAwarding)
                    || ($label == 'Applications' && $needsReviewing)
                ): ?>
                    <i class="fa-solid fa-circle-exclamation" style="color: red;"></i>
                <?php endif; ?>

                <?php if ($label == 'Funding Cycles' && $fundingCycleWarning): ?>
                    <i class="fa-solid fa-triangle-exclamation" style="color: orange;"></i>
                <?php endif; ?>

                <?php if ($label == 'Funding Cycles' && $fundingCycleAlert): ?>
                    <i class="fa-solid fa-circle-exclamation" style="color: red;"></i>
                <?php endif; ?>
            </button>
        </li>
    <?php endforeach; ?>
</ul>

<div class="tab-content" id="admin-index">
    <?php foreach ($tabs as $label => $id): ?>
        <div
            class="tab-pane <?= $id === array_values($tabs)[0] ? 'active' : '' ?>"
            id="<?= $id ?>-tab-pane"
            role="tabpanel"
            aria-labelledby="<?= $id ?>-tab"
            tabindex="0"
        >
            <?= $this->element("Admin/index/$id") ?>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // Remember selected tab
    window.onload = function() {
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function (tab) {
            tab.addEventListener('click', function (e) {
                localStorage.setItem('activeTabId', e.target.id);
            });
        });
        const activeTabId = localStorage.getItem('activeTabId');
        if (activeTabId) {
            const activeTab = new bootstrap.Tab('#' + activeTabId);
            activeTab.show();
        }
    };
</script>
