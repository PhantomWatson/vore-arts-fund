<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\View\AppView $this
 */
?>
<tr>
    <th>
        Status
    </th>
    <td>
        <?= $project->status_name ?>
    </td>
</tr>

<?= $this->element('Projects/overview_public') ?>

<tr>
    <th>
        Accept partial payout?
    </th>
    <td>
        <?= $project->accept_partial_payout ? 'Yes' : 'No' ?>
    </td>
</tr>
<tr>
    <th>
        Make check out to
    </th>
    <td>
        <?= $project->check_name ?: '<span class="no-answer">No answer</span>' ?>
    </td>
</tr>
