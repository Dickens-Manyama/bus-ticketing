<?php
/** @var yii\web\View $this */
use common\models\Booking;
use yii\helpers\Html;

$this->title = 'Bookings Report';

$totalBookings = Booking::find()->count();
$statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
$statusCounts = [];
foreach ($statuses as $status) {
    $statusCounts[$status] = Booking::find()->where(['status' => $status])->count();
}
$recentBookings = Booking::find()->orderBy(['created_at' => SORT_DESC])->limit(10)->all();

// Bootstrap color map for statuses
$statusColors = [
    'pending' => 'warning',
    'confirmed' => 'success',
    'cancelled' => 'danger',
    'completed' => 'primary',
];
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
    <button class="btn btn-outline-success" onclick="exportTableToCSV('bookings-report.csv', 'bookings-table')"><i class="bi bi-file-earmark-excel"></i> Export to CSV</button>
</div>
<h1 class="mb-4">Bookings Report</h1>
<div class="row mb-4 fade-in">
    <div class="col-md-3 mb-2">
        <div class="card text-white bg-info h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Total Bookings</h5>
                <span class="display-6 fw-bold"><?= $totalBookings ?></span>
            </div>
        </div>
    </div>
    <?php foreach ($statuses as $status): ?>
        <div class="col-md-2 mb-2">
            <div class="card text-white bg-<?= $statusColors[$status] ?> h-100">
                <div class="card-body text-center">
                    <h6 class="card-title mb-1 text-capitalize"><?= $status ?></h6>
                    <span class="fw-bold display-7"><?= $statusCounts[$status] ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="col-md-3 mb-2">
        <canvas id="bookingsStatusChart" height="80"></canvas>
    </div>
</div>
<h3 class="mt-4">Recent Bookings</h3>
<table id="bookings-table" class="table table-bordered table-striped table-hover fade-in">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Bus</th>
            <th>Route</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentBookings as $i => $booking): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><?= Html::encode($booking->user ? $booking->user->username : '-') ?></td>
                <td><?= Html::encode($booking->bus ? $booking->bus->type : '-') ?> (<?= Html::encode($booking->bus ? $booking->bus->plate_number : '-') ?>)</td>
                <td><?= Html::encode($booking->route ? $booking->route->origin : '-') ?> → <?= Html::encode($booking->route ? $booking->route->destination : '-') ?></td>
                <td><span class="badge bg-<?= $statusColors[$booking->status] ?? 'secondary' ?>"><?= Html::encode($booking->status) ?></span></td>
                <td><?= date('Y-m-d H:i', $booking->created_at) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('bookingsStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_map('ucfirst', $statuses)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($statusCounts)) ?>,
                backgroundColor: [
                    '#ffc107', // warning
                    '#198754', // success
                    '#dc3545', // danger
                    '#0d6efd', // primary
                ],
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