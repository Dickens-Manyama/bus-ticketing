<?php
/** @var yii\web\View $this */
use common\models\Bus;
use yii\helpers\Html;

$this->title = 'Fleet Report';

$totalBuses = Bus::find()->count();
$buses = Bus::find()->all();
// Count by class (use class property, not label)
$classLabels = [
    Bus::CLASS_LUXURY => 'Luxury',
    Bus::CLASS_SEMI_LUXURY => 'Semi-Luxury',
    Bus::CLASS_MIDDLE_CLASS => 'Middle Class',
];
$classColors = [
    Bus::CLASS_LUXURY => '#198754',
    Bus::CLASS_SEMI_LUXURY => '#ffc107',
    Bus::CLASS_MIDDLE_CLASS => '#0d6efd',
];
$classCounts = [
    Bus::CLASS_LUXURY => 0,
    Bus::CLASS_SEMI_LUXURY => 0,
    Bus::CLASS_MIDDLE_CLASS => 0,
];
foreach ($buses as $bus) {
    if (isset($classCounts[$bus->class])) {
        $classCounts[$bus->class]++;
    }
}
?>
<style>
.fade-in { animation: fadeIn 1s; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.table-hover tbody tr:hover { background-color: #f1f3f4; transition: background 0.3s; }
.print-hide { display: inline-block; }
@media print { .print-hide { display: none !important; } }
</style>
<div class="d-flex justify-content-end align-items-center mb-2 print-hide fade-in">
    <button class="btn btn-outline-primary me-2" onclick="window.print()"><i class="bi bi-printer"></i> Print Report</button>
    <button class="btn btn-outline-success" onclick="exportTableToCSV('fleet-report.csv', 'fleet-table')"><i class="bi bi-file-earmark-excel"></i> Export to CSV</button>
</div>
<h1 class="mb-4">Fleet Report</h1>
<div class="row mb-4 fade-in">
    <div class="col-md-4 mb-2">
        <div class="card text-white bg-primary h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Total Buses</h5>
                <span class="display-6 fw-bold"><?= $totalBuses ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-8 mb-2">
        <canvas id="fleetClassChart" height="80"></canvas>
    </div>
</div>
<h3 class="mt-4">Fleet Details</h3>
<table id="fleet-table" class="table table-bordered table-striped table-hover fade-in">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>Type</th>
            <th>Class</th>
            <th>Plate Number</th>
            <th>Seat Count</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($buses as $i => $bus): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><?= Html::encode($bus->type) ?></td>
                <td><span class="badge" style="background: <?= $classColors[$bus->class] ?? '#6c757d' ?>; color: #fff;">
                    <?= Html::encode($classLabels[$bus->class] ?? $bus->class) ?></span></td>
                <td><?= Html::encode($bus->plate_number) ?></td>
                <td><?= Html::encode($bus->seat_count) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('fleetClassChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_values($classLabels)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($classCounts)) ?>,
                backgroundColor: <?= json_encode(array_values($classColors)) ?>,
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: { display: true, position: 'bottom' }
            },
            animation: { animateScale: true }
        }
    });
});
// Export table to CSV
function exportTableToCSV(filename, tableId) {
    var csv = [];
    var rows = document.querySelectorAll('#' + tableId + ' tr');
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('th, td');
        for (var j = 0; j < cols.length; j++)
            row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
        csv.push(row.join(","));
    }
    var csvFile = new Blob([csv.join("\n")], { type: 'text/csv' });
    var downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script> 