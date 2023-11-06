<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\View\AppView $this
 */
?>
<tr>
    <th>
        Applicant
    </th>
    <td>
        <?= $project->user->name ?>
    </td>
</tr>
<tr>
    <th>
        Category
    </th>
    <td>
        <?= $project->category->name ?>
    </td>
</tr>
<tr>
    <th>
        Funding cycle
    </th>
    <td>
        <?= $this->element('FundingCycles/link', ['fundingCycle' => $project->funding_cycle]) ?>
    </td>
</tr>
<tr>
    <th>
        Amount requested
    </th>
    <td>
        $<?= number_format($project->amount_requested) ?>
    </td>
</tr>
<tr>
    <th>
        Reports
    </th>
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
            None
        <?php endif; ?>
    </td>
</tr>
