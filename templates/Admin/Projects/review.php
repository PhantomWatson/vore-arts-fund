<?php

use App\Model\Entity\Note;
use App\Model\Entity\Project;

/**
 * @var Project $project
 * @var Note $newNote
 * @var Note[]|\Cake\ORM\ResultSet $notes
 * @var \App\Model\Entity\Question[] $questions
 * @var \App\View\AppView $this
 * @var int[] $statusActions
 * @var int[] $validStatusIds
 * @var \App\Model\Entity\Transaction[]|\Cake\ORM\ResultSet $transactions
 */

$transactionsTabLabel = 'Transactions' . ($transactions->isEmpty() ? null : ' (' . $transactions->count() . ')');
$tabs = [
    'Overview' => 'overview',
    'Description' => 'description',
    $transactionsTabLabel => 'transactions',
    'Notes & Messages' => 'notes'
];

$this->Html->css('/viewerjs/viewer.min.css', ['block' => true]);
$alertIcon = '<i class="fa-solid fa-circle-exclamation" style="color: red;"></i>';
?>

<ul class="nav nav-tabs" id="review-tabs" role="tablist">
    <?php foreach ($tabs as $label => $tabId): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tabId == 'overview' ? 'active' : '' ?>" id="<?= $tabId ?>-tab" data-bs-toggle="tab"
                    data-bs-target="#<?= $tabId ?>-section" type="button" role="tab" aria-controls="<?= $tabId ?>-section"
                    aria-selected="<?= $tabId == 'overview' ? 'true' : 'false' ?>">
                <?= $label ?>
                <?php if ($tabId == 'notes' && !$notes->isEmpty()): ?>
                    (<?= number_format(count($notes)) ?>)
                <?php endif; ?>
            </button>
        </li>
    <?php endforeach; ?>
</ul>

<div class="tab-content mt-3">
    <div class="tab-pane show active" id="overview-section" role="tabpanel" aria-labelledby="overview-tab">
        <section>
            <h2>
                Application
            </h2>
            <table class="table project-overview-table">
                <tbody>
                <tr>
                    <th>
                        Status
                    </th>
                    <td>
                        <?= $project->status_name ?>
                        <?php if ($validStatusIds): ?>
                            <div class="dropdown review-inline-dropdown">
                                <button class="btn btn-primary dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                >
                                    Update
                                </button>
                                <ul class="dropdown-menu" id="status-change">
                                    <?php foreach ($validStatusIds as $statusId): ?>
                                        <li>
                                            <button class="dropdown-item review-dropdown-item"
                                                    data-status-id="<?= $statusId ?>"
                                                    type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#review-modal-<?= $statusId ?>"
                                            >
                                                <?= $statusActions[$statusId]['icon'] ?>
                                                <?= $statusActions[$statusId]['label'] ?>
                                            </button>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Applicant
                    </th>
                    <td>
                        <?= $project->user->name ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Category
                    </th>
                    <td>
                        <?= $project->category->name ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Funding cycle
                    </th>
                    <td>
                        <?= $this->element('FundingCycles/link', [
                            'fundingCycle' => $project->funding_cycle,
                            'append' => '',
                        ]) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Amount requested
                    </th>
                    <td>
                        <?= $project->amount_requested_formatted ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </section>

        <?php if ($project->isAwarded()): ?>
            <section>
                <h2>Loan</h2>
                <table class="table project-overview-table">
                    <tbody>
                    <tr>
                        <th>
                            Amount awarded
                        </th>
                        <td>
                            <?= $project->amount_awarded_formatted ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Loan agreement
                        </th>
                        <td>
                            <?php if ($project->loan_agreement_date): ?>
                                Signed on <?= $project->loan_agreement_date_local->format('F j, Y') ?>
                            <?php else: ?>
                                <?= $alertIcon ?> Not signed
                            <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <section>
                <h2>Check</h2>
                <table class="table project-overview-table">
                    <tbody>
                    <tr>
                        <th>
                            Date mailed
                        </th>
                        <td>
                            <?php
                                $disbursementDates = $project->disbursement_dates_local;
                                if ($disbursementDates) {
                                    foreach ($disbursementDates as $i => $date) {
                                        if ($i > 0) {
                                            echo '<br />';
                                        }
                                        echo $date->format('F j, Y');
                                    }
                                } else {
                                    echo $alertIcon . ' Not mailed yet';
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Amount
                        </th>
                        <td>
                            <?= $project->amount_awarded
                                ? $project->amount_awarded_formatted
                                : ($alertIcon . ' Amount awarded is zero? Something\'s wrong here.') ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Payable to
                        </th>
                        <td>
                            <?= $project->check_name ?: ($alertIcon . ' Check name not provided, but applicant\'s name is ' . $project->user->name) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Mail to
                        </th>
                        <td>
                            <?php if ($project->address): ?>
                                <?= $project->address ?>
                                <br />
                                Muncie, IN <?= $project->zip ?>
                            <?php else: ?>
                                <?= $alertIcon ?> Not provided
                            <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <section>
                <h2>
                    Post-award stuff
                </h2>
                <table class="table project-overview-table">
                    <tbody>
                    <tr>
                        <th>
                            Tax ID number
                        </th>
                        <td>
                            <?php if ($project->tin): ?>
                                Collected
                                <?= $this->Html->link(
                                    'Retrieve',
                                    [
                                        'controller' => 'Projects',
                                        'action' => 'getTin',
                                        'id' => $project->id,
                                    ],
                                    [
                                        'class' => 'btn btn-secondary btn-sm',
                                    ]
                                ) ?>
                            <?php else: ?>
                                Not collected
                                <?php if (!$project->requires_tin): ?>
                                    (not required)
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Reports
                        </th>
                        <td>
                            <?php if (count($project->reports)): ?>
                                <?= $this->Html->link(
                                    count($project->reports) . ' (view)',
                                    [
                                        'prefix' => false,
                                        'controller' => 'Reports',
                                        'action' => 'project',
                                        $project->id,
                                    ]
                                ) ?>
                            <?php else: ?>
                                None
                            <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>
        <?php endif; ?>
    </div>
    <div class="tab-pane" id="description-section" role="tabpanel" aria-labelledby="description-tab">
        <?= $this->element('Projects/description') ?>
    </div>
    <div class="tab-pane" id="transactions-section" role="tabpanel" aria-labelledby="transactions-tab">
        <section>
            <h3>
                Transactions
            </h3>
            <?php if ($transactions->isEmpty()): ?>
                <p>
                    No transactions are associated with this project
                </p>
            <?php else: ?>
                <table id="review-transactions" class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Amount (net)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td>
                                <?= $transaction->date?->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('M j, Y g:ia') ?>
                            </td>
                            <td>
                                <?= $transaction->type_name ?>
                            </td>
                            <td>
                                <?= $transaction->name ?>
                            </td>
                            <td>
                                <?= $transaction->dollar_amount_gross_formatted ?>
                                (<?= $transaction->dollar_amount_net_formatted ?>)
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>
    <div class="tab-pane" id="notes-section" role="tabpanel" aria-labelledby="notes-tab">
        <section>
            <h3>
                Notes & Messages
            </h3>
            <p>
                <button class="btn btn-primary"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#review-modal-note"
                >
                    <?= Project::ICON_NOTE ?>
                    &nbsp;
                    Add a note
                </button>
                <button class="btn btn-primary"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#review-modal-message"
                >
                    <?= Project::ICON_MESSAGE ?>
                    &nbsp;
                    Send a message
                </button>
            </p>
            <div id="review-notes">
                <?php if ($notes->isEmpty()): ?>
                    None found for this project
                <?php else: ?>
                    <?php foreach ($notes as $note): ?>
                        <?= $this->element('Notes/view', compact('note')) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<?php foreach ($validStatusIds as $statusId): ?>
    <?= $this->element('Admin/Projects/status_change_modal', compact('statusId', 'project')) ?>
<?php endforeach; ?>
<?php foreach ([Note::TYPE_NOTE, Note::TYPE_MESSAGE_TO_APPLICANT] as $noteType): ?>
    <?= $this->element('Admin/Projects/note_or_message_modal', compact('project', 'noteType')) ?>
<?php endforeach; ?>

<?= $this->Image->initViewer() ?>

<?php if ($this->getRequest()->getQuery('amountAwarded')): ?>
    <script>
        document.querySelector('button[data-bs-target="#review-modal-5"]').click();
    </script>
<?php endif; ?>
