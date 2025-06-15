<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Report>|\Cake\ORM\ResultSet $reports
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

<p>
    <?= $this->Html->link(
        'Submit report',
        [
            'prefix' => 'My',
            'controller' => 'Reports',
            'action' => 'submit'
        ],
        ['class' => 'btn btn-primary']
    ) ?>
</p>

<div class="reports">
    <?php if (count($reports) === 0): ?>
        <p class="alert alert-info">
            You have not submitted any reports yet.
        </p>
    <?php else: ?>
        <table class="table">
            <thead>
            <tr>

                <th>Report</th>
                <th>Project</th>
                <th>Funding Cycle</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td>
                        <?= $this->Html->link(
                            $report->created->format('F j, Y') . ($report->is_final ? ' (final)' : ''),
                            [
                                'prefix' => false,
                                'controller' => 'Reports',
                                'action' => 'view',
                                'id' => $report->id,
                            ]
                        ) ?>
                    </td>
                    <td>
                        <?= $this->Html->link($report->project->title, [
                            'prefix' => 'My',
                            'controller' => 'Projects',
                            'action' => 'view',
                            'id' => $report->project_id
                        ]) ?>
                    </td>
                    <td>
                        <?= $report->project->funding_cycle->name ?? 'Unknown' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
