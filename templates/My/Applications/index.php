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
    <table class="table" id="my-applications">
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
                        <?= $this->Html->link(
                            $application->title,
                            [
                                'controller' => 'Applications',
                                'action' => 'viewMy',
                                'id' => $application->id,
                            ],
                        ) ?>
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
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>

                            <ul class="dropdown-menu">
                                <?= $this->Html->link(
                                    '<i class="fa-solid fa-eye"></i> View application',
                                    [
                                        'controller' => 'Applications',
                                        'action' => 'viewMy',
                                        'id' => $application->id,
                                    ],
                                    [
                                        'class' => 'dropdown-item',
                                        'escape' => false
                                    ]
                                ) ?>
                                <?php if (in_array($application->status_id, $updateWhen)): ?>
                                    <?= $this->Html->link(
                                        '<i class="fa-solid fa-pencil"></i> Update / Submit',
                                        [
                                            'controller' => 'Applications',
                                            'action' => 'edit',
                                            'id' => $application->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item',
                                            'escape' => false
                                        ]
                                    ) ?>
                                    <?= $this->Form->postLink(
                                        '<i class="fa-solid fa-trash"></i> Delete',
                                        [
                                            'prefix' => 'My',
                                            'controller' => 'Applications',
                                            'action' => 'delete',
                                            'id' => $application->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item',
                                            'confirm' => 'Are you sure you want to delete this application?',
                                            'escape' => false
                                        ]
                                    ) ?>
                                <?php endif; ?>
                                <?php if (in_array($application->status_id, $withdrawWhen)): ?>
                                    <?= $this->Html->link(
                                        '<i class="fa-solid fa-ban"></i> Withdraw',
                                        [
                                            'controller' => 'Applications',
                                            'action' => 'withdraw',
                                            'id' => $application->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item',
                                            'escape' => false
                                        ]
                                    ) ?>
                                <?php endif; ?>
                                <?php if (in_array($application->status_id, $reportWhen)): ?>
                                    <?= $this->Html->link(
                                        '<i class="fa-solid fa-file-lines"></i> Submit report',
                                        [
                                            'prefix' => false,
                                            'controller' => 'Reports',
                                            'action' => 'submit',
                                            $application->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item',
                                            'escape' => false
                                        ]
                                    ) ?>
                                <?php endif; ?>
                                <?php if (count($application->reports)): ?>
                                    <?= $this->Html->link(
                                        '<i class="fa-solid fa-file-lines"></i> View reports',
                                        [
                                            'prefix' => false,
                                            'controller' => 'Reports',
                                            'action' => 'application',
                                            $application->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item',
                                            'escape' => false
                                        ]
                                    ) ?>
                                <?php endif; ?>

                            </ul>
                        </div>
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
