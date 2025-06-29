<?php
/**
 * @var \App\View\AppView $this
 * @var Project[] $projects
 */

use App\Model\Entity\Project;

?>

<?php if (empty($projects)): ?>
    <p>
        You have not yet received a loan from the Vore Arts Fund.
    </p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Loan ID</th>
                <th>Project</th>
                <th>Awarded Date</th>
                <th>Loan Amount</th>
                <th>Balance</th>
                <th>View Details</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td>
                        <?= h($project->id) ?>
                    </td>
                    <td>
                        <?= h($project->title) ?>
                    </td>
                    <td>
                        <?= $project->loan_awarded_date_formatted ?>
                    </td>
                    <td>
                        <?= $project->amount_awarded_formatted_cents ?>
                    </td>
                    <td>
                        <?php
                            // Convert repaid amount to dollars
                            $balance = $project->amount_awarded - ($project->getTotalRepaid() / 100);
                        ?>
                        <?php if ($balance < 0): ?>
                            $0 <span class="text-success">
                                (fully paid + extra
                                <?= '$' . number_format(-$balance, 2) ?>
                                donation)
                            </span>
                        <?php elseif ($balance == 0): ?>
                            $0 <span class="text-success">(fully paid)</span>
                        <?php else: ?>
                            $<?= number_format($balance, 2) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>

                            <ul class="dropdown-menu">
                                <li>
                                    <?= $this->Html->link(
                                        Project::ICON_NOTE . ' Payment history',
                                        [
                                            'controller' => 'Loans',
                                            'action' => 'view',
                                            'id' => $project->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item dropdown-item__with-icon',
                                            'escape' => false
                                        ]
                                    ) ?>
                                </li>
                                <li>
                                    <?= $this->Html->link(
                                        Project::ICON_FUND . ' Make payment',
                                        [
                                            'controller' => 'Loans',
                                            'action' => 'payment',
                                            'id' => $project->id,
                                        ],
                                        [
                                            'class' => 'dropdown-item dropdown-item__with-icon',
                                            'escape' => false
                                        ]
                                    ) ?>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
