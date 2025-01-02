<?php
/**
 * @var \App\Model\Entity\FundingCycle[] $fundingCycles
 * @var string $title
 */
$suggestedCycles = [];
function monthName($monthNumber) {
    return date('M', mktime(0, 0, 0, $monthNumber, 1));
}
for ($n = 1; $n <= 12; $n++) {
    $monthName = monthName($n);
    $suggestedCycles[$monthName] = [
        monthName($n - 2) => 'App',
        monthName($n - 1) => 'Review + Vote',
        monthName($n) => 'Disburse',
    ];
}
?>

<?php if ($fundingCycles->count()): ?>
    <?php foreach ($fundingCycles as $fundingCycle): ?>
        <section class="funding-cycles">
            <h2>
                <?= $fundingCycle->name ?>
            </h2>
            <?= $this->element('FundingCycles/info_table', compact('fundingCycle')) ?>
        </section>
    <?php endforeach; ?>
<?php else: ?>
    <?= $this->element('no_funding_cycle') ?>
<?php endif; ?>

<table class="table table-bordered" id="cycles-chart">
    <thead>
        <tr>
            <th>Cycle</th>
            <?php foreach (explode(' ', 'Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec') as $month): ?>
                <th><?= $month ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($suggestedCycles as $month => $cycle): ?>
            <tr class="spring">
                <th><?= $month ?></th>
                <?php for ($n = 1; $n <= 12; $n++): ?>
                    <?php $monthName = monthName($n); ?>
                    <?php if ($cycle[$monthName] ?? false): ?>
                        <td>
                            <?= $cycle[$monthName] ?? '' ?>
                        </td>
                    <?php else: ?>
                        <td class="blank"></td>
                    <?php endif; ?>
                <?php endfor; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
