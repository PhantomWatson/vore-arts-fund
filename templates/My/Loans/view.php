<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\Transaction[] $repayments
 */
$totalRepaid = $project->getTotalRepaid() / 100; // In dollars
$balance = $project->amount_awarded - $totalRepaid;
?>

<table class="table table-striped">
    <tr>
        <th>Project</th>
        <td><?= h($project->title) ?></td>
    </tr>
    <tr>
        <th>Loan Amount</th>
        <td><?= $project->amount_awarded_formatted_cents ?></td>
    </tr>
    <?php if ($repayments): ?>
        <tr>
            <th>Repayments</th>
            <td>
                <ul class="list-unstyled">
                <?php foreach ($repayments as $repayment): ?>
                    <li>
                        <?= $repayment->date?->setTimezone(\App\Application::LOCAL_TIMEZONE)->format('F j, Y') ?>:
                        <?= $repayment->dollar_amount_net_formatted ?>
                        <?php if ($repayment->processing_fee_formatted): ?>
                            (+ <?= $repayment->processing_fee_formatted ?> processing fee)
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <th>Total repaid</th>
        <td>$<?= number_format($totalRepaid, 2) ?></td>
    </tr>
    <tr>
        <th>Balance</th>
        <td>
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
    </tr>
</table>

<p>
    <strong>Congratulations!</strong> You've fully repaid the loan for this project.
</p>

<p>
    <?php if ($overpayment): ?>
        Furthermore, by overpaying, you've made an additional tax-deductible donation of <?= $overpayment ?> to the Vore Arts Fund, which helps us maintain and expand the support that we provide to the community. Thank you for your generosity!
    <?php else: ?>
        If you would like to support the Vore Arts Fund further, please consider making a donation. Your tax-deductible contributions help us maintain and expand the support that we provide to the community.
    <?php endif; ?>
</p>
