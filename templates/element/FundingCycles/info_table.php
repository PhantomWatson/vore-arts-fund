<?php
/**
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\Model\Entity\User $authUser
 * @var \App\View\AppView $this
 */

$votesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Votes');
$hasVoted = $authUser && $votesTable->hasVoted($authUser->id, $fundingCycle->id);
$resultsSummary = '';
$projectSummary = $fundingCycle->getProjectsSummary();
if ($projectSummary) {
    $resultsSummary .= sprintf(
        '%s %s submitted',
        $projectSummary['submitted'],
        __n('application', 'applications', $projectSummary['submitted'])
    );
    $isAll = false;
    if ($projectSummary['submitted']) {
        $isAll = $projectSummary['accepted'] == $projectSummary['submitted'];
        $resultsSummary .= sprintf(
            ', %s accepted',
            $isAll ? 'all' : $projectSummary['accepted']
        );

        if ($projectSummary['accepted']) {
            $isAll = $projectSummary['awarded'] == $projectSummary['submitted'];
            $resultsSummary .= sprintf(
                ', %s awarded',
                $isAll ? 'all' : $projectSummary['awarded']
            );
        }
        $resultsSummary .= (!$isAll || !$fundingCycle->is_finalized) ? ' so far' : null;
    }
}
?>
<table class="table table-striped funding-cycle-info_table">
    <tbody>
    <tr>
        <th>
            Application period
        </th>
        <td>
            <div class="row">
                <div class="<?= $fundingCycle->isCurrentlyApplying() ? 'col-md-6' : 'col' ?>">
                    <?= $fundingCycle->application_begin_local->format('F j, Y') ?>
                    to
                    <?= $fundingCycle->application_end_local->format('F j, Y') ?>
                </div>
                <?php if ($fundingCycle->isCurrentlyApplying()): ?>
                    <div class="col-md-6">
                        <?= $this->Html->link(
                            'Apply for funding',
                            [
                                'controller' => 'Projects',
                                'action' => 'apply',
                            ],
                            ['class' => 'btn btn-primary']
                        ) ?>
                    </div>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <tr>
        <th>
            Voting period
        </th>
        <td>
            <div class="row">
                <div class="<?= $hasVoted || $fundingCycle->isCurrentlyVoting() ? 'col-md-6' : 'col' ?>">
                    <?= $fundingCycle->vote_begin_local->format('F j, Y') ?>
                    to
                    <?= $fundingCycle->vote_end_local->format('F j, Y') ?>
                </div>
                <?php if ($hasVoted || $fundingCycle->isCurrentlyVoting()): ?>
                    <div class="col-md-6">
                        <?php if ($hasVoted): ?>
                            <strong>Thanks for voting!</strong>
                        <?php elseif ($fundingCycle->isCurrentlyVoting()): ?>
                            <?= $this->Html->link(
                                'Cast your votes',
                                ['controller' => 'Votes', 'action' => 'index', 'prefix' => false],
                                ['class' => 'btn btn-primary']
                            ) ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <tr>
        <th>
            Loans awarded
        </th>
        <td>
            <?= $fundingCycle->name ?>
        </td>
    </tr>
    <tr>
        <th>
            Budget
        </th>
        <td>
            <?= $fundingCycle->funding_available_formatted ?>
        </td>
    </tr>
    <?php if ($resultsSummary): ?>
        <tr>
            <th>
                Results
            </th>
            <td>
                <?= $resultsSummary ?>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
