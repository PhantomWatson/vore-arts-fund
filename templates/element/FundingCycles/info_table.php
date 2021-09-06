<?php
/** @var \App\Model\Entity\FundingCycle $fundingCycle */
?>
<table class="table">
    <tbody>
    <tr>
        <th>
            Applications accepted
        </th>
        <td>
            <?= $fundingCycle->application_begin->format('F j, Y') ?>
            to
            <?= $fundingCycle->application_end->format('F j, Y') ?>
        </td>
    </tr>
    <tr>
        <th>
            Voting
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
    </tbody>
</table>
