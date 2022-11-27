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
            'controller' => 'Applications',
            'action' => 'apply',
        ],
        ['class' => 'btn btn-primary']
    ) ?>
</p>

<?php if ($applications->count()): ?>
    <?php foreach ($applications as $application): ?>
        <section class="my-applications__application bordered-section">
            <h2>
                <?= $application->title ?>
            </h2>
            <table class="table">
                <tbody>
                    <tr>
                        <th>Created:</th>
                        <td>
                            <?= $application->created->format('F j, Y') ?>
                            <br />
                            <?= $application->funding_cycle->name ?> funding cycle
                        </td>
                        <td>
                            <?= $this->Html->link(
                                'View',
                                [
                                    'controller' => 'Applications',
                                    'action' => 'viewMy',
                                    'id' => $application->id,
                                    '?' => ['back' => Router::url()],
                                ],
                                ['title' => 'View application', 'class' => 'btn btn-secondary']
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Status:</th>
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
                        </td>
                    </tr>
                    <?php if (in_array($application->status_id, $reportWhen)): ?>
                        <tr>
                            <th>Reports:</th>
                            <td>
                                <?php $count = count($application->reports); ?>
                                <?php if ($count): ?>
                                    <?= $this->Html->link(
                                        sprintf(
                                            '%s %s submitted',
                                            $count,
                                            __n('report', 'reports', $count)
                                        ),
                                        [
                                            'controller' => 'Reports',
                                            'action' => 'application',
                                            $application->id,
                                            '?' => ['back' => Router::url()],
                                        ]
                                    ) ?>
                                <?php else: ?>
                                    No reports submitted
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $this->Html->link(
                                    'Submit report',
                                    [
                                        'controller' => 'Reports',
                                        'action' => 'submit',
                                        $application->id,
                                    ],
                                    ['class' => 'btn btn-secondary']
                                ) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    <?php endforeach; ?>
<?php else: ?>
    <p class="alert alert-info">
        You have not yet submitted any applications for funding.
    </p>
<?php endif; ?>
