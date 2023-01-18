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
            <?= $fundingCycle->application_begin->format('F j, Y') ?>
            to
            <?= $fundingCycle->application_end->format('F j, Y') ?>
        </td>
    </tr>
    <tr>
        <th>
            Voting period
        </th>
        <td>
            <?= $fundingCycle->vote_begin->format('F j, Y') ?>
            to
            <?= $fundingCycle->vote_end->format('F j, Y') ?>
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
    <?php $applicationSummary = $fundingCycle->getApplicationSummary(); ?>
    <?php if ($applicationSummary): ?>
        <tr>
            <th>
                Results
            </th>
            <td>
                <?php
                    echo $applicationSummary['submitted']
                        . ' '
                        . __n('application', 'applications', $applicationSummary['submitted'])
                        . ' submitted';
                    if ($applicationSummary['submitted']) {
                        echo ', ';
                        echo $applicationSummary['accepted'] == $applicationSummary['submitted']
                            ? 'all'
                            : $applicationSummary['accepted'];
                        echo ' accepted';
                        if (
                            $applicationSummary['submitted'] != $applicationSummary['accepted']
                            && $fundingCycle->vote_begin->isFuture()
                        ) {
                            echo ' so far';
                        }

                        if ($applicationSummary['accepted']) {
                            echo ', ';
                            echo $applicationSummary['awarded'] == $applicationSummary['submitted']
                                ? 'all'
                                : $applicationSummary['awarded'];
                            echo ' awarded';
                        }
                    }
                ?>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
