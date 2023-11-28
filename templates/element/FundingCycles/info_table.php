<?php
/** @var \App\Model\Entity\FundingCycle $fundingCycle */
?>
<table class="table">
    <tbody>
    <tr>
        <th>
            Application period
        </th>
        <td>
            <?= $fundingCycle->application_begin_local->format('F j, Y') ?>
            to
            <?= $fundingCycle->application_end_local->format('F j, Y') ?>
        </td>
    </tr>
    <tr>
        <th>
            Voting period
        </th>
        <td>
            <?= $fundingCycle->vote_begin_local->format('F j, Y') ?>
            to
            <?= $fundingCycle->vote_end_local->format('F j, Y') ?>
        </td>
    </tr>
    <tr>
        <th>
            Budget
        </th>
        <td>
            <?=
            $fundingCycle->funding_available
                ? '$' . number_format($fundingCycle->funding_available)
                : 'Not yet determined'
            ?>
        </td>
    </tr>
    <?php $projectSummary = $fundingCycle->getProjectsSummary(); ?>
    <?php if ($projectSummary): ?>
        <tr>
            <th>
                Results
            </th>
            <td>
                <?php
                    echo $projectSummary['submitted']
                        . ' '
                        . __n('application', 'applications', $projectSummary['submitted'])
                        . ' submitted';
                    if ($projectSummary['submitted']) {
                        echo ', ';
                        echo $projectSummary['accepted'] == $projectSummary['submitted']
                            ? 'all'
                            : $projectSummary['accepted'];
                        echo ' accepted';
                        if (
                            $projectSummary['submitted'] != $projectSummary['accepted']
                            && $fundingCycle->vote_begin_local->isFuture()
                        ) {
                            echo ' so far';
                        }

                        if ($projectSummary['accepted']) {
                            echo ', ';
                            echo $projectSummary['awarded'] == $projectSummary['submitted']
                                ? 'all'
                                : $projectSummary['awarded'];
                            echo ' awarded';
                        }
                    }
                ?>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
