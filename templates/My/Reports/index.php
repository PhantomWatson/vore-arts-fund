<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Project>|\Cake\ORM\ResultSet $projects
 */
?>

<div class="alert alert-info">
    <p>
        We (and the IRS) require reports for every project that receives funding. We'd love to get an update whenever you have incremental victories to celebrate or challenges to share, but at a minimum, we need a report to be submitted at the conclusion of your project and at least one report per year if you have a multi-year project.
    </p>

    <p>
        These reports don't need to be long or formal, but should address the current state of the project and how you've used your funding. These will be shared with the public, so don't include any private or irrelevant information.
    </p>
</div>

<div class="reports">
    <?php if (count($projects) === 0): ?>
        <p class="alert alert-info">
            You do not need to submit any reports at this time.
        </p>
    <?php else: ?>
        <table class="table">
            <thead>
            <tr>
                <th>Project</th>
                <th>Reports</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td>
                        <?= $this->Html->link($project->title, [
                            'prefix' => 'My',
                            'controller' => 'Projects',
                            'action' => 'view',
                            'id' => $project->id
                        ]) ?>
                        <br />
                        <span style="font-size: 0.8em">
                            <?= $project->funding_cycle->name ?? 'Unknown' ?> funding cycle
                        </span>
                    </td>
                    <td>
                        <?php if (count($project->reports) === 0): ?>
                            No reports submitted yet
                        <?php else: ?>
                            <?php foreach ($project->reports as $report): ?>
                                <?= $this->Html->link(
                                    $report->created->format('F j, Y') . ($report->is_final ? ' (final)' : ''),
                                    [
                                        'prefix' => false,
                                        'controller' => 'Reports',
                                        'action' => 'view',
                                        'id' => $report->id,
                                    ]
                                ) ?>
                                <br />
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($project->is_finalized): ?>
                            Final report submitted
                        <?php else: ?>
                            <?= $this->Html->link(
                                'Submit report',
                                [
                                    'prefix' => 'My',
                                    'controller' => 'Reports',
                                    'action' => 'submit',
                                    'id' => $project->id
                                ],
                                ['class' => 'btn btn-primary']
                            ) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
