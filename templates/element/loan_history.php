<?php
/**
 * @var \App\View\AppView $this
 * @var array<int, array{date: \Cake\I18n\FrozenTime, loansOutstanding: float, loansRepaid: float}> $loanHistory
 */
$this->Html->script('https://www.gstatic.com/charts/loader.js', ['block' => true]);
?>

<script>
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            //['Genre', 'Fantasy & Sci Fi', 'Romance', 'Mystery/Crime', 'General', 'Western', 'Literature', { role: 'annotation' } ],
            ['Date', 'Repaid loans', 'Outstanding loans'],
            <?php foreach ($loanHistory as $bar): ?>
            [
                new Date(<?= json_encode($bar['date']->format('Y-m-d')) ?>),
                <?= json_encode($bar['loansRepaid']) ?>,
                <?= json_encode($bar['loansOutstanding']) ?>,
            ],
            <?php endforeach; ?>
        ]);

        var options = {
            width: '100%',
            height: 300,
            legend: { position: 'bottom' },
            bar: { groupWidth: '75%' },
            isStacked: true,
            vAxis: {
                format: '$#,###',

            },
            hAxis: {
                format: 'MMM yy',
            },
            colors: ['#1A5B11', 'rgb(228, 230, 195)'],
        };

        const dateFormatter = new google.visualization.DateFormat({ pattern: 'MMMM yyyy' });
        dateFormatter.format(data, 0);

        const currencyFormatter = new google.visualization.NumberFormat({
            prefix: '$', // Use your desired currency symbol
            decimalSymbol: '.',
            groupingSymbol: ',',
            fractionDigits: 0,
        });
        currencyFormatter.format(data, 1);
        currencyFormatter.format(data, 2);

        var chart = new google.visualization.ColumnChart(document.getElementById("loans-chart"));
        chart.draw(data, options);
    }
</script>
<div id="loans-chart" style="width: 100%; height: 300px;"></div>
