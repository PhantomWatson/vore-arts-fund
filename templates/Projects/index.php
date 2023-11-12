<?php
/**
 * @var \App\View\AppView $this
 * @var Project[] $projects
 */

// TODO: Link to vote, if applicable

use App\Model\Entity\Project;

?>

<p>
    Commercial arts-related projects in Muncie, Indiana are eligible to apply for loans through the Vore Arts Fund to
    cover their up-front costs. After every round of public voting, some projects get funded, and others can re-apply
    and try again in the following funding cycle. For more details, visit our
    <?= $this->Html->link(
        'about',
        ['controller' => 'Pages', 'action' => 'about']
    ) ?> page.
</p>

<?php if ($projects): ?>
    <?= $this->element('pagination') ?>

    <table class="table">
        <thead>
            <tr>
                <th>
                    Project
                </th>
                <th>Status</th>
                <th>Funding cycle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td>
                        <?= $this->Html->link(
                            $project->title,
                            [
                                'prefix' => false,
                                'controller' => 'Projects',
                                'action' => 'view',
                                'id' => $project->id,
                            ]
                        ) ?>
                        (<?= $project->category->name ?>)
                    </td>
                    <td>
                        <?php
                            switch($project->status_id) {
                                case Project::STATUS_ACCEPTED:
                                    if ($project->funding_cycle->isCurrentlyVoting()) {
                                        echo $this->Html->link(
                                            'Voting currently underway',
                                            ['controller' => 'Votes', 'action' => 'index', 'prefix' => false],
                                        );
                                    } elseif ($project->funding_cycle->votingHasPassed()) {
                                        echo 'Awaiting funding decision';
                                    } else {
                                        echo 'Awaiting voting';
                                    }
                                    break;
                                case Project::STATUS_AWARDED:
                                    echo 'Funding awarded';
                                    break;
                                case Project::STATUS_NOT_AWARDED:
                                    echo 'Funding not awarded';
                            }
                        ?>
                    </td>
                    <td>
                        <?= $this->element(
                            'FundingCycles/link',
                            [
                                'fundingCycle' => $project->funding_cycle,
                                'append' => '',
                            ]
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?= $this->element('pagination') ?>
<?php else: ?>
    <p>
        Please check back later for a list of projects that have applied for funding.
    </p>
<?php endif; ?>
