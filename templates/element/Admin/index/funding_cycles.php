<?php
/**
 * @var \App\View\AppView $this
 * @var array{
 *     takingApplications: \Cake\ORM\ResultSet<\App\Model\Entity\FundingCycle>,
 *     voting: \Cake\ORM\ResultSet<\App\Model\Entity\FundingCycle>,
 *     nextTakingApplications: \App\Model\Entity\FundingCycle|null,
 *     needsAwarding: \Cake\ORM\ResultSet<\App\Model\Entity\FundingCycle>,
 * } $fundingCycles
 */
?>

<div class="card">
    <div class="card-header">
        <h2>
            Funding cycles
        </h2>
        <ul class="links">
            <li>
                <?= $this->Html->link('Manage', [
                    'prefix' => 'Admin',
                    'controller' => 'FundingCycles',
                    'action' => 'index',
                ]) ?>
            </li>
            <li>
                <?= $this->Html->link('Add', [
                    'prefix' => 'Admin',
                    'controller' => 'FundingCycles',
                    'action' => 'add',
                ]) ?>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <?php if ($fundingCycles['takingApplications']->count() > 1): ?>
            <p class="alert alert-warning">
                Oh crap, there aren't supposed to be multiple funding cycles taking applications at the same time.
            </p>
        <?php endif; ?>
        <?php if ($fundingCycles['voting']->count() > 1): ?>
            <p class="alert alert-warning">
                Oh crap, there aren't supposed to be multiple funding cycles accepting votes at the same time.
            </p>
        <?php endif; ?>
        <?php if ($fundingCycles['needsAwarding']->count() > 1): ?>
            <p class="alert alert-warning">
                There shouldn't be multiple funding cycles that need awarding at the same time.
            </p>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>
                        Type
                    </th>
                    <th>
                        Cycle
                    </th>
                    <th>
                        Schedule
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if ($fundingCycles['takingApplications']->isEmpty()): ?>
                    <p class="alert alert-error">
                        Not currently taking applications
                    </p>
                <?php else: ?>
                    <?php foreach ($fundingCycles['takingApplications'] as $fundingCycle): ?>
                        <tr>
                            <td>
                                Taking applications
                            </td>
                            <td>
                                <?= $this->Html->link(
                                    $fundingCycle->name,
                                    [
                                        'prefix' => 'Admin',
                                        'controller' => 'FundingCycles',
                                        'action' => 'edit',
                                        $fundingCycle->id,
                                    ]
                                ) ?>
                            </td>
                            <td>
                                until <?= $fundingCycle->application_end_local->format('F j, Y') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (!$fundingCycles['voting']->isEmpty()): ?>
                    <?php foreach ($fundingCycles['voting'] as $fundingCycle): ?>
                        <tr>
                            <td>
                                Voting
                            </td>
                            <td>
                                <?= $this->Html->link(
                                    $fundingCycle->name,
                                    [
                                        'prefix' => 'Admin',
                                        'controller' => 'FundingCycles',
                                        'action' => 'edit',
                                        $fundingCycle->id,
                                    ]
                                ) ?>
                            </td>
                            <td>
                                until <?= $fundingCycle->vote_end_local->format('F j, Y') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (!$fundingCycles['needsAwarding']->isEmpty()): ?>
                    <?php foreach ($fundingCycles['needsAwarding'] as $fundingCycle): ?>
                        <tr>
                            <td>
                                Needs awarding
                                <a href="#awarding-anchor">
                                    <i class="fa-solid fa-circle-exclamation" style="color: red;"></i>
                                </a>
                            </td>
                            <td colspan="2">
                                <?= $this->Html->link(
                                    $fundingCycle->name,
                                    [
                                        'prefix' => 'Admin',
                                        'controller' => 'FundingCycles',
                                        'action' => 'edit',
                                        $fundingCycle->id,
                                    ]
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($fundingCycles['nextTakingApplications']): ?>
                    <tr>
                        <td>
                            Next
                        </td>
                        <td>
                            <?= $this->Html->link(
                                $fundingCycles['nextTakingApplications']->name,
                                [
                                    'prefix' => 'Admin',
                                    'controller' => 'FundingCycles',
                                    'action' => 'edit',
                                    $fundingCycles['nextTakingApplications']->id,
                                ]
                            ) ?>
                        </td>
                        <td>
                            taking applications on <?= $fundingCycles['nextTakingApplications']->application_begin_local->format('F j, Y') ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td>
                            Next
                        </td>
                        <td colspan="2">
                            <span class="badge text-bg-danger">
                                No funding cycles are currently taking applications.
                            </span>
                            <?= $this->Html->link(
                                'Add one',
                                [
                                    'prefix' => 'Admin',
                                    'controller' => 'FundingCycles',
                                    'action' => 'add',
                                ],
                                ['class' => 'btn btn-sm btn-primary ms-2']
                            ) ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
