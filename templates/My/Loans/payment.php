<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var Transaction[] $repayments
 */

use App\Model\Entity\Transaction;

$totalRepaid = $project->getTotalRepaid() / 100; // In dollars
$balance = $project->amount_awarded - $totalRepaid;
$isPaidOff = $balance <= 0;
?>

<?php if ($isPaidOff): ?>
    <div class="alert alert-success">
        Congratulations! This loan is fully paid off. If you would like to thank the Vore Arts Fund for its support of your project, please consider visiting our
        <?= $this->Html->link(
            'donation page',
            ['prefix' => false, 'controller' => 'Pages', 'action' => 'donate']
        ) ?> and making a tax-deductible contribution.
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <p>
            This loan still has an outstanding balance of <strong>$<?= number_format($balance, 2) ?></strong>.
        </p>
        <p>
            Payments made electronically will incur a <strong>processing fee of <?= Transaction::STRIPE_FEE_DISPLAYED ?></strong>, which will not be applied to your loan balance.
        </p>
        <p>
            If you wish to donate by check, please read <?= $this->Html->link(
                'our instructions for mailing checks',
                ['prefix' => false, 'controller' => 'Pages', 'action' => 'checks'],
            ) ?> and note that this payment is for <strong>Loan #<?= $project->id ?></strong>.
        </p>
        <p>
            Note that loan repayments are not considered tax-deductible donations, but showing your support for the Vore Arts Fund by <strong>overpaying</strong> is deeply appreciated, and any overpayment <em>can</em> be claimed as a tax-deductible donation to the Vore Arts Fund.
        </p>
    </div>

    <div id="root"></div>
    <?= $this->element('load_app_files', ['jsType' => 'module']) ?>
    <script>
        window.repaymentFormData = {
            projectId: <?= json_encode($project->id) ?>,
            balance: <?= json_encode($balance) ?>,
            processingFee: {
                flat: <?= json_encode(Transaction::STRIPE_FEE_FIXED) ?>,
                percentage: <?= json_encode(Transaction::STRIPE_FEE_PERCENTAGE) ?>,
            }
        };
    </script>
<?php endif; ?>
