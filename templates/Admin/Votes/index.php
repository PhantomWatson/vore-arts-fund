<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project[] $projects
 */
?>

<?php if ($projects): ?>
    <table class="table">
        <thead>
            <tr>
                <th>
                    Rank
                </th>
                <th>
                    Project
                </th>
                <th>
                    Total score
                </th>
                <th>
                    Funding requested
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $i => $project): ?>
                <tr>
                    <td>
                        <?= $project->voting_score == null ? '' : $i + 1 ?>
                    </td>
                    <td>
                        <?= $project->title ?>
                    </td>
                    <td>
                        <?= $project->voting_score === null ? 'No votes' : $project->voting_score ?>
                    </td>
                    <td>
                        <?= $project->amount_requested_formatted ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>
        No projects found in this funding cycle
    </p>
<?php endif; ?>
