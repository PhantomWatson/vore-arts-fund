<?php
/**
 * @var \App\Model\Entity\Project[]|\Cake\ORM\ResultSet $projects
 * @var \App\View\AppView $this
 */

use App\Model\Entity\Project;
use Cake\Routing\Router;
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
                <th class="cell--created">Created</th>
                <th class="cell--status">Status</th>
                <th class="cell--messages">Messages</th>
                <th class="cell--reports">Reports</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td class="cell--project">
                        <?= $this->Html->link(
                            $project->title,
                            [
                                'controller' => 'Projects',
                                'action' => 'view',
                                'id' => $project->id,
                            ],
                        ) ?>
                        <div class="responsive-details">
                            <ul>
                                <li>
                                    <?= $project->created->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('M j, \'y') ?>
                                    (<?= $this->element(
                                        'FundingCycles/link',
                                        ['fundingCycle' => $project->funding_cycle, 'append' => '']
                                    ) ?>)
                                </li>
                                <li>
                                    Status: <?= $project->status_name ?>
                                </li>
                                <?php if (count($project->notes)): ?>
                                    <li>
                                        <?= $this->Html->link(
                                            count($project->notes) . __n(' message', ' messages', count($project->notes)),
                                            [
                                                'prefix' => 'My',
                                                'controller' => 'Projects',
                                                'action' => 'messages',
                                                'id' => $project->id,
                                            ]
                                        ) ?>
                                    </li>
                                <?php endif; ?>
                                <?php if (count($project->reports)): ?>
                                    <li>
                                        <?= $this->Html->link(
                                            count($project->reports) . __n(' report', ' reports', count($project->reports)),
                                            [
                                                'prefix' => false,
                                                'controller' => 'Reports',
                                                'action' => 'projects',
                                                $project->id,
                                            ]
                                        ) ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
                    <td class="cell--created">
                        <?= $project->created->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y') ?>
                        <br />
                        <?= $this->element('FundingCycles/link', ['fundingCycle' => $project->funding_cycle]) ?>
                    </td>
                    <td class="cell--status">
                        <?= $project->status_name ?>
                    </td>
                    <td class="cell--messages">
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
                    <td class="cell--reports">
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
                                <?= $this->element('Projects/actions', compact('project')) ?>
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
