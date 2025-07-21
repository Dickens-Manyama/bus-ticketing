<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Booking Statistics';
$this->params['breadcrumbs'][] = ['label' => 'My Bookings', 'url' => ['my-bookings']];
$this->params['breadcrumbs'][] = $this->title;

// Register Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_END]);
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-graph-up text-primary"></i> My Booking Statistics
                </h1>
                <div>
                    <?= Html::a('<i class="bi bi-arrow-left"></i> Back to My Bookings', ['my-bookings'], ['class' => 'btn btn-outline-secondary']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-ticket-detailed display-4"></i>
                    <h4 class="card-title mt-2"><?= $totalBookings ?></h4>
                    <p class="card-text">Total Bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle display-4"></i>
                    <h4 class="card-title mt-2"><?= $confirmedBookings ?></h4>
                    <p class="card-text">Confirmed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle display-4"></i>
                    <h4 class="card-title mt-2"><?= $cancelledBookings ?></h4>
                    <p class="card-text">Cancelled</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin display-4"></i>
                    <h4 class="card-title mt-2"><?= number_format($totalSpent) ?></h4>
                    <p class="card-text">Total Spent (TZS)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Booking Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Monthly Booking Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Most Frequent Routes -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-star"></i> Most Frequent Routes</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($frequentRoutes)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($frequentRoutes as $route): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= Html::encode($route['route']['origin'] ?? 'Unknown') ?> → <?= Html::encode($route['route']['destination'] ?? 'Unknown') ?></strong>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?= $route['count'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">No frequent routes yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Bookings</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentBookings)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Bus</th>
                                        <th>Route</th>
                                        <th>Seat</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentBookings as $booking): ?>
                                        <tr>
                                            <td>#<?= $booking->id ?></td>
                                            <td>
                                                <strong><?= Html::encode($booking->bus->type) ?></strong><br>
                                                <small class="text-muted"><?= Html::encode($booking->bus->plate_number) ?></small>
                                            </td>
                                            <td>
                                                <?= Html::encode($booking->route->origin) ?> → <?= Html::encode($booking->route->destination) ?><br>
                                                <small class="text-muted"><?= number_format($booking->route->price) ?> TZS</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= Html::encode($booking->seat->seat_number) ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'confirmed' => 'success',
                                                    'cancelled' => 'danger',
                                                    'pending' => 'warning',
                                                ];
                                                $statusClass = $statusClass[$booking->status] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($booking->status) ?></span>
                                            </td>
                                            <td><?= date('M j, Y', $booking->created_at) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <?= Html::a('<i class="bi bi-eye"></i>', ['receipt', 'id' => $booking->id], ['class' => 'btn btn-outline-primary', 'title' => 'View Receipt']) ?>
                                                    <?= Html::a('<i class="bi bi-download"></i>', ['pdf-receipt', 'id' => $booking->id], ['class' => 'btn btn-outline-secondary', 'title' => 'Download PDF']) ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">No recent bookings found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Prepare chart data
$months = [];
$counts = [];
foreach (array_reverse($monthlyBookings) as $data) {
    $months[] = date('M Y', strtotime($data['month']));
    $counts[] = $data['count'];
}

$monthsJson = json_encode($months);
$countsJson = json_encode($counts);
$js = <<<JS
// Monthly Booking Trend Chart
var ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {$monthsJson},
        datasets: [{
            label: 'Bookings per Month',
            data: {$countsJson},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
JS;
$this->registerJs($js);
?> 