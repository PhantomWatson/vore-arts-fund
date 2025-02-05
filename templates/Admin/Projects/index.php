<?php
/**
 * @var \App\Model\Entity\Project[]|\Cake\ORM\ResultSet $projects
 * @var \App\Model\Entity\FundingCycle[]|\Cake\ORM\ResultSet $fundingCycles
 * @var int $fundingCycleId
 */
?>
<?php if ($fundingCycles): ?>
    <?= $this->element('funding_cycle_selector', [
        'url' => [
            'prefix' => 'Admin',
            'controller' => 'Projects',
            'action' => 'index',
        ],
        'fundingCycleId' => $fundingCycleId,
    ]) ?>

    <?php if (count($projects)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?= $project->title ?></td>
                        <td><?= $project->status_name ?></td>
                        <td><?= $this->Html->link(
                                'View',
                                [
                                    'prefix' => 'Admin',
                                    'controller' => 'Projects',
                                    'action' => 'review',
                                    'id' => $project['id'],
                                ],
                                ['class' => 'btn btn-secondary']
                            ) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($fundingCycleId): ?>
        <p class="alert alert-info">
            No applications have been received for this funding cycle
        </p>
    <?php else: ?>
        <p class="alert alert-info">
            Please select a funding cycle
        </p>
    <?php endif; ?>
<?php else: ?>
    <p class="alert alert-info">
        No funding cycles found
    </p>
<?php endif; ?>
