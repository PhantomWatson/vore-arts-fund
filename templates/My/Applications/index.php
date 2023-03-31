<?php
/**
 * @var \App\Model\Entity\Application[]|\Cake\ORM\ResultSet $applications
 * @var \App\View\AppView $this
 */

use App\Model\Entity\Application;
use Cake\Routing\Router;

$updateWhen = [
    Application::STATUS_DRAFT,
    Application::STATUS_REVISION_REQUESTED,
];
$withdrawWhen = [
    Application::STATUS_UNDER_REVIEW,
    Application::STATUS_ACCEPTED,
];
$reportWhen = [
    Application::STATUS_AWARDED,
];
?>

<p>
    <?= $this->Html->link(
        'Submit a new application for funding',
        [
            'prefix' => false,
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
                <th>Project</th>
                <th>Created</th>
                <th>Status</th>
                <th>Reports</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td>
                        <?= $application->title ?>
                    </td>
                    <td>
                        <?= $application->created->format('F j, Y') ?>
                        <br />
                        <?= $this->element('FundingCycles/link', ['fundingCycle' => $application->funding_cycle]) ?>
                    </td>
                    <td>
                        <?= $application->status_name ?>
                    </td>
                    <td>
                        <?php if (count($application->reports)): ?>
                            <?= $this->Html->link(
                                count($application->reports),
                                [
                                    'prefix' => false,
                                    'controller' => 'Reports',
                                    'action' => 'application',
                                    $application->id,
                                ]
                            ) ?>
                        <?php else: ?>
                            <?= count($application->reports) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $this->Html->link(
                            'View',
                            [
                                'controller' => 'Applications',
                                'action' => 'viewMy',
                                'id' => $application->id,
                            ],
                            ['title' => 'View application', 'class' => 'btn btn-secondary']
                        ) ?>
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
                        <?php if (in_array($application->status_id, $reportWhen)): ?>
                            <?= $this->Html->link(
                                'Submit report',
                                [
                                    'prefix' => false,
                                    'controller' => 'Reports',
                                    'action' => 'submit',
                                    $application->id,
                                ],
                                ['class' => 'btn btn-secondary']
                            ) ?>
                        <?php endif; ?>
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
