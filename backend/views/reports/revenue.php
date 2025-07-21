<?php
/** @var yii\web\View $this */
use common\models\Booking;
use yii\helpers\Html;

$this->title = 'Revenue Report';

$totalRevenue = Booking::find()->joinWith('route')->sum('route.price');
$recentBookings = Booking::find()->orderBy(['created_at' => SORT_DESC])->limit(10)->all();
// Revenue per month (last 6 months)
$monthlyRevenue = [];
$monthLabels = [];
for ($i = 5; $i >= 0; $i--) {
    $start = strtotime(date('Y-m-01', strtotime("-$i months")));
    $end = strtotime(date('Y-m-t 23:59:59', strtotime("-$i months")));
    $monthLabel = date('F Y', $start);
    $monthLabels[] = $monthLabel;
    $monthlyRevenue[$monthLabel] = Booking::find()->joinWith('route')
        ->where(['between', 'booking.created_at', $start, $end])
        ->sum('route.price');
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
    <button class="btn btn-outline-success" onclick="exportTableToCSV('revenue-report.csv', 'revenue-table')"><i class="bi bi-file-earmark-excel"></i> Export to CSV</button>
</div>
<h1 class="mb-4">Revenue Report</h1>
<div class="row mb-4 fade-in">
    <div class="col-md-4 mb-2">
        <div class="card text-white bg-success h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Total Revenue</h5>
                <span class="display-6 fw-bold"><?= number_format($totalRevenue) ?> TZS</span>
            </div>
        </div>
    </div>
    <div class="col-md-8 mb-2">
        <canvas id="revenueMonthChart" height="80"></canvas>
    </div>
</div>
<h3 class="mt-4">Recent Bookings (Revenue)</h3>
<table id="revenue-table" class="table table-bordered table-striped table-hover fade-in">
    <thead class="table-success">
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Route</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentBookings as $i => $booking): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><?= Html::encode($booking->user ? $booking->user->username : '-') ?></td>
                <td><?= Html::encode($booking->route ? $booking->route->origin : '-') ?> → <?= Html::encode($booking->route ? $booking->route->destination : '-') ?></td>
                <td><span class="badge bg-success"><?= number_format($booking->route ? $booking->route->price : 0) ?> TZS</span></td>
                <td><?= date('Y-m-d H:i', $booking->created_at) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('revenueMonthChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($monthLabels) ?>,
            datasets: [{
                label: 'Revenue (TZS)',
                data: <?= json_encode(array_values($monthlyRevenue)) ?>,
                backgroundColor: '#198754',
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