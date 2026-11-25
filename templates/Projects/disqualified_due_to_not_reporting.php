<?php
/**
 * @var \App\View\AppView $this
 * @var array<\App\Model\Entity\Project> $disqualifiedProjects
 */
?>

<p>
    You and any organization you may be applying on behalf of have been disqualified from applying for funding due to
    a failure to submit required reports for the following projects.
</p>

<table class="table">
    <thead>
        <tr>
            <th>Project</th>
            <th>Loan awarded date</th>
            <th>Loan agreement</th>
            <th>Report dates</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($disqualifiedProjects as $project): ?>
            <tr>
                <td>
                    <?= $this->Html->link(
                        $project->title,
                        [
                            'prefix' => 'My',
                            'controller' => 'Projects',
                            'action' => 'view',
                            'id' => $project->id,
                        ],
                    ) ?>
                </td>
                <td>
                    <?= $project->loan_awarded_date->format('F j, Y') ?>
                </td>
                <td>
                    <?= $this->Html->link(
                        'View',
                        [
                            'prefix' => 'My',
                            'controller' => 'Loans',
                            'action' => 'loanAgreement',
                            'id' => $project->id,
                        ]
                    ) ?>
                </td>
                <td>
                    <?php
                        if ($project->reports) {
                            $reportDates = $project->reports->map(function ($report) {
                                return $report->created->format('F j, Y');
                            })->toArray();
                            echo implode('<br />', $reportDates);
                        } else {
                            echo '(none)';
                        }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
