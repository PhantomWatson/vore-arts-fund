<?php
/**
 * @var \App\Model\Entity\Project[]|\Cake\ORM\ResultSet $projects
 * @var \App\View\AppView $this
 */

use App\Model\Entity\Project;
use Cake\Routing\Router;

$updateWhen = [
    Project::STATUS_DRAFT,
    Project::STATUS_REVISION_REQUESTED,
];
$withdrawWhen = [
    Project::STATUS_UNDER_REVIEW,
    Project::STATUS_ACCEPTED,
];
$reportWhen = [
    Project::STATUS_AWARDED,
];
?>

<p>
    <?= $this->Html->link(
        'Submit a new application for funding',
        [
            'prefix' => false,
            'controller' => 'Projects',
            'action' => 'apply',
        ],
        ['class' => 'btn btn-primary']
    ) ?>
</p>

<?php if ($projects->count()): ?>
    <table class="table" id="my-projects">
        <thead>
            <tr>
                <th>Project</th>
                <th>Created</th>
                <th>Status</th>
                <th>Messages</th>
                <th>Reports</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td>
                        <?= $this->Html->link(
                            $project->title,
                            [
                                'controller' => 'Projects',
                                'action' => 'view',
                                'id' => $project->id,
                            ],
                        ) ?>
                    </td>
                    <td>
                        <?= $project->created->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y') ?>
                        <br />
                        <?= $this->element('FundingCycles/link', ['fundingCycle' => $project->funding_cycle]) ?>
                    </td>
                    <td>
                        <?= $project->status_name ?>
                    </td>
                    <td>
                        <?php if (count($project->notes)): ?>
                            <?= $this->Html->link(
                                count($project->notes) . ' (view)',
                                [
                                    'prefix' => 'My',
                                    'controller' => 'Projects',
                                    'action' => 'messages',
                                    'id' => $project->id,
                                ]
                            ) ?>
                        <?php else: ?>
                            <?= count($project->notes) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (count($project->reports)): ?>
                            <?= $this->Html->link(
                                count($project->reports) . ' (view)',
                                [
                                    'prefix' => false,
                                    'controller' => 'Reports',
                                    'action' => 'projects',
                                    $project->id,
                                ]
                            ) ?>
                        <?php else: ?>
                            <?= count($project->reports) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>

                            <ul class="dropdown-menu">
                                <?= $this->Html->link(
                                    '<i class="fa-solid fa-eye"></i> View project',
                                    [
                                        'controller' => 'Projects',
                                        'action' => 'view',
                                        'id' => $project->id,
                                    ],
                                    [
                                        'class' => 'dropdown-item',
                                        'escape' => false
                                    ]
                                ) ?>
                                <?php if (in_array($project->status_id, $updateWhen)): ?>
                                    <?= $this->Html->link(
                                        '<i class="fa-solid fa-pencil"></i> Update / Submit',
                                        [
                                            'controller' => 'Projects',
                                            'action' => 'edit',
                                            'id' => $project->id,
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
                                            'controller' => 'Projects',
                                            'action' => 'delete',
                                            'id' => $project->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item',
                                            'confirm' => 'Are you sure you want to delete this application?',
                                            'escape' => false
                                        ]
                                    ) ?>
                                <?php endif; ?>
                                <?php if (in_array($project->status_id, $withdrawWhen)): ?>
                                    <?= $this->Form->postLink(
                                        Project::ICON_WITHDRAW . ' Withdraw',
                                        [
                                            'controller' => 'Projects',
                                            'action' => 'withdraw',
                                            'id' => $project->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item',
                                            'escape' => false,
                                            'confirm' => 'Are you sure you want to withdraw this application?',
                                        ]
                                    ) ?>
                                <?php endif; ?>
                                <?php if (in_array($project->status_id, $reportWhen)): ?>
                                    <?= $this->Html->link(
                                        Project::ICON_REPORT . ' Submit report',
                                        [
                                            'prefix' => false,
                                            'controller' => 'Reports',
                                            'action' => 'submit',
                                            $project->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item',
                                            'escape' => false
                                        ]
                                    ) ?>
                                <?php endif; ?>
                                <?php if (count($project->notes)): ?>
                                    <?= $this->Html->link(
                                        Project::ICON_MESSAGE . ' View messages',
                                        [
                                            'prefix' => 'My',
                                            'controller' => 'Projects',
                                            'action' => 'messages',
                                            $project->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item',
                                            'escape' => false
                                        ]
                                    ) ?>
                                <?php endif; ?>
                                <?php if (count($project->reports)): ?>
                                    <?= $this->Html->link(
                                        Project::ICON_REPORT . ' View reports',
                                        [
                                            'prefix' => false,
                                            'controller' => 'Reports',
                                            'action' => 'projects',
                                            $project->id,
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
        You have not yet submitted any projects for funding.
    </p>
<?php endif; ?>
