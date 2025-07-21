<?php
/** @var yii\web\View $this */
use common\models\Route;
use yii\helpers\Html;

$this->title = 'Route Report';

$routes = Route::find()->all();
$routeStats = [];
foreach ($routes as $route) {
    $bookingCount = $route->getBookings()->count();
    $revenue = $bookingCount * $route->price;
    $routeStats[] = [
        'route' => $route,
        'bookings' => $bookingCount,
        'revenue' => $revenue,
    ];
}
// Sort by revenue descending for top routes
usort($routeStats, function($a, $b) { return $b['revenue'] <=> $a['revenue']; });
$topRoutes = array_slice($routeStats, 0, 5);
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
    <button class="btn btn-outline-success" onclick="exportTableToCSV('route-report.csv', 'route-table')"><i class="bi bi-file-earmark-excel"></i> Export to CSV</button>
</div>
<h1 class="mb-4">Route Report</h1>
<div class="row mb-4 fade-in">
    <div class="col-md-4 mb-2">
        <div class="card text-white bg-info h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Total Routes</h5>
                <span class="display-6 fw-bold"><?= count($routes) ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-8 mb-2">
        <canvas id="topRoutesChart" height="80"></canvas>
    </div>
</div>
<h3 class="mt-4">Top Performing Routes</h3>
<table id="route-table" class="table table-bordered table-striped table-hover fade-in">
    <thead class="table-info">
        <tr>
            <th>#</th>
            <th>Route</th>
            <th>Bookings</th>
            <th>Revenue</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($routeStats as $i => $stat): ?>
            <tr<?= $i < 5 ? ' style="font-weight:bold;background:#e3f2fd;"' : '' ?>>
                <td><?= $i+1 ?></td>
                <td><?= Html::encode($stat['route']->origin) ?> → <?= Html::encode($stat['route']->destination) ?></td>
                <td><?= $stat['bookings'] ?></td>
                <td><span class="badge bg-info text-dark"><?= number_format($stat['revenue']) ?> TZS</span></td>
                <td><?= number_format($stat['route']->price) ?> TZS</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('topRoutesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(function($r) { return $r['route']->origin . ' → ' . $r['route']->destination; }, $topRoutes)) ?>,
            datasets: [{
                label: 'Revenue (TZS)',
                data: <?= json_encode(array_map(function($r) { return $r['revenue']; }, $topRoutes)) ?>,
                backgroundColor: '#0d6efd',
                borderColor: '#145c32',
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            animation: { animateScale: true },
            scales: {
                y: { beginAtZero: true }
            }
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
<p>This is a placeholder for the Route Report. Implement report details here.</p> 