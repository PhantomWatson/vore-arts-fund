<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet<\App\Model\Entity\Project> $applications
 * @var array{
 *      takingApplications: \Cake\ORM\ResultSet<\App\Model\Entity\FundingCycle>
 *  } $fundingCycles
 */
?>

<div class="card">
    <div class="card-header">
        <h2>
            Applications
        </h2>
        <ul class="links">
            <li>
                <?= $this->Html->link('Manage questions', [
                    'prefix' => 'Admin',
                    'controller' => 'Questions',
                    'action' => 'index',
                ]) ?>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <?php if ($fundingCycles['takingApplications']->isEmpty()): ?>
            <p>
                <span class="badge text-bg-danger">
                    Not currently accepting applications
                </span>

                <?= $this->Html->link(
                    'Add a funding cycle',
                    [
                        'prefix' => 'Admin',
                        'controller' => 'FundingCycles',
                        'action' => 'add',
                    ],
                    ['class' => 'btn btn-sm btn-primary ms-2']
                ) ?>
            </p>
        <?php elseif ($applications): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Applicant</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td>
                                <?= $this->Html->link(
                                    $application->title,
                                    [
                                        'prefix' => 'Admin',
                                        'controller' => 'Projects',
                                        'action' => 'review',
                                        'id' => $application->id,
                                    ],
                                ) ?>
                            </td>
                            <td>
                                <?= h($application->user->name) ?>
                            </td>
                            <td>
                                <?= $application->status_name ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>
                No applications yet
            </p>
        <?php endif; ?>
    </div>
</div>
