<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet<\App\Model\Entity\Report> $recentReports
 */
?>

<div class="card">
    <div class="card-header">
        <h2>
            Reports
        </h2>
        <ul class="links">
            <li>
                <?= $this->Html->link('View all', [
                    'prefix' => false,
                    'controller' => 'Reports',
                    'action' => 'index',
                ]) ?>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <?php if ($recentReports->isEmpty()): ?>
            <p>
                No reports have been submitted recently.
            </p>
        <?php else: ?>
            <?= $this->element('report', ['report' => $recentReports->first()]) ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Recent reports</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentReports as $report): ?>
                        <tr>
                            <td>
                                <?= $report->project->title ?>
                            </td>
                            <td>
                                <?= $this->Html->link(
                                    $report->date_local->format('F j, Y'),
                                    [
                                        'prefix' => false,
                                        'controller' => 'Reports',
                                        'action' => 'view',
                                        $report->id,
                                    ]
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
