<?php
/**
 * @var \App\Model\Entity\Application[]|\Cake\ORM\ResultSet $applications
 * @var \App\View\AppView $this
 */

use App\Model\Entity\Application;

$updateWhen = [
    Application::STATUS_DRAFT,
    Application::STATUS_REVISION_REQUESTED,
];
$withdrawWhen = [
    Application::STATUS_UNDER_REVIEW,
    Application::STATUS_VOTING
];

?>
<?= $this->title() ?>

<p>
    <?= $this->Html->link(
        'Submit a new application for funding',
        [
            'controller' => 'Applications',
            'action' => 'apply',
        ],
        ['class' => 'btn btn-primary']
    ) ?>
</p>

<?php if ($applications->count()): ?>
    <table class="table">
        <thead>
            <tr>
                <th>
                    Funding Cycle
                </th>
                <th>
                    Status
                </th>
                <th>
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td>
                        <?= $application->funding_cycle->name ?>
                    </td>
                    <td>
                        <?= $application->status_name ?>
                    </td>
                    <td>
                        <?php if (in_array($application->status_id, $updateWhen)): ?>
                            <?= $this->Html->link(
                                'Update / Submit',
                                [
                                    'controller' => 'Applications',
                                    'action' => 'edit',
                                    'id' => $application->id,
                                ],
                                ['class' => 'btn btn-secondary']
                            ) ?>
                        <?php endif; ?>
                        <?php if (in_array($application->status_id, $withdrawWhen)): ?>
                            <?= $this->Html->link(
                                'Withdraw',
                                [
                                    'controller' => 'Applications',
                                    'action' => 'withdraw',
                                    'id' => $application->id,
                                ],
                                ['class' => 'btn btn-secondary']
                            ) ?>
                        <?php endif; ?>
                        <?= $this->Html->link(
                            'View',
                            [
                                'controller' => 'Applications',
                                'action' => 'viewMy',
                                'id' => $application->id,
                            ],
                            ['class' => 'btn btn-secondary']
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="alert alert-info">
        You have not yet submitted any applications for funding.
    </p>
<?php endif; ?>
