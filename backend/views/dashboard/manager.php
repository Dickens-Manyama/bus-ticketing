<?php
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $totalBookings int */
/* @var $pendingBookings int */
/* @var $activeBookings int */
/* @var $totalBuses int */
/* @var $totalRoutes int */
/* @var $todayBookings int */
/* @var $weekBookings int */
/* @var $monthBookings int */
/* @var $routePerformance array */
/* @var $recentBookings common\models\Booking[] */

$this->title = 'Manager Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Bootstrap Icons
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
// Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => View::POS_END]);

$routeLabels = [];
$routeBookings = [];
$routeRevenue = [];

foreach ($routePerformance as $route) {
    $routeLabels[] = $route['origin'] . ' → ' . $route['destination'];
    $routeBookings[] = $route['bookings'];
    $routeRevenue[] = $route['avg_price'];
}

$routeLabelsJson = json_encode($routeLabels);
$routeBookingsJson = json_encode($routeBookings);
$routeRevenueJson = json_encode($routeRevenue);

$js = <<<JS
// Route Performance Chart
var ctx1 = document.getElementById('routePerformanceChart').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: $routeLabelsJson,
        datasets: [{
            label: 'Bookings per Route',
            data: $routeBookingsJson,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2
        }]
    },
    options: {scales: {y: {beginAtZero: true}}, plugins: {legend: {display: false}}}
});

// Revenue per Route Chart
var ctx2 = document.getElementById('routeRevenueChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: $routeLabelsJson,
        datasets: [{
            data: $routeRevenueJson,
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 205, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ]
        }]
    },
    options: {plugins: {legend: {position: 'bottom'}}}
});
JS;
$this->registerJs($js);

$user = Yii::$app->user->identity;
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-success sidebar py-4 shadow-sm rounded-end-4">
            <div class="sidebar-sticky">
                <div class="text-center mb-4">
                    <i class="bi bi-person-workspace display-4 text-white"></i>
                    <h5 class="mt-2 mb-0 fw-bold text-white"><?= Html::encode($user->username) ?></h5>
                    <small class="text-light">Operations Manager</small>
                </div>
                <ul class="nav flex-column mb-4">
                    <li class="nav-item mb-2">
                        <a class="nav-link active fw-bold text-white" href="#"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/booking/index'])) ?>"><i class="bi bi-ticket-detailed me-2"></i> Bookings</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/bus/index'])) ?>"><i class="bi bi-bus-front me-2"></i> Fleet</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/route/index'])) ?>"><i class="bi bi-geo-alt me-2"></i> Routes</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="#"><i class="bi bi-calendar-check me-2"></i> Schedule</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/index'])) ?>"><i class="bi bi-clipboard-data me-2"></i> Reports</a>
                    </li>
                </ul>
                <div class="alert alert-success text-center small mb-0">
                    <i class="bi bi-check-circle"></i> Operations Management Access
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="bi bi-person-workspace text-success"></i> Operations Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-success">Export Data</button>
                        <button type="button" class="btn btn-sm btn-outline-success">Print Report</button>
                    </div>
                </div>
            </div>

            <!-- Operational Overview Cards -->
            <div class="d-flex flex-wrap gap-3 mb-4">
                <div class="card text-white bg-success mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-ticket-detailed display-5"></i>
                        <h5 class="card-title mt-2">Total Bookings</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $totalBookings ?></p>
                    </div>
                </div>
                <div class="card text-white bg-warning mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-clock display-5"></i>
                        <h5 class="card-title mt-2">Pending</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $pendingBookings ?></p>
                    </div>
                </div>
                <div class="card text-white bg-info mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle display-5"></i>
                        <h5 class="card-title mt-2">Active</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $activeBookings ?></p>
                    </div>
                </div>
                <div class="card text-white bg-primary mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-bus-front display-5"></i>
                        <h5 class="card-title mt-2">Fleet Size</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $totalBuses ?></p>
                    </div>
                </div>
                <div class="card text-white bg-secondary mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-geo-alt display-5"></i>
                        <h5 class="card-title mt-2">Active Routes</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $totalRoutes ?></p>
                    </div>
                </div>
            </div>

            <!-- Time-based Metrics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-day text-success display-4"></i>
                            <h5 class="card-title">Today's Bookings</h5>
                            <p class="card-text display-6 fw-bold text-success"><?= $todayBookings ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-week text-info display-4"></i>
                            <h5 class="card-title">This Week</h5>
                            <p class="card-text display-6 fw-bold text-info"><?= $weekBookings ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-month text-primary display-4"></i>
                            <h5 class="card-title">This Month</h5>
                            <p class="card-text display-6 fw-bold text-primary"><?= $monthBookings ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Route Performance -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="bi bi-graph-up"></i> Route Performance Analysis
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <h6>Bookings per Route</h6>
                                    <canvas id="routePerformanceChart" height="120"></canvas>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <h6>Revenue Distribution</h6>
                                    <canvas id="routeRevenueChart" height="120"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white fw-bold">
                            <i class="bi bi-list-check"></i> Operational Status
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> All buses operational</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Routes running on time</li>
                                <li class="mb-2"><i class="bi bi-exclamation-circle-fill text-warning"></i> 2 drivers on leave</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Maintenance up to date</li>
                                <li class="mb-2"><i class="bi bi-info-circle-fill text-info"></i> Weather: Clear</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark fw-bold">
                            <i class="bi bi-exclamation-triangle"></i> Alerts
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="bi bi-clock text-warning"></i> Route A1: 15 min delay</li>
                                <li class="mb-2"><i class="bi bi-info-circle text-info"></i> Bus B3: Maintenance due</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success"></i> All systems normal</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-clock-history"></i> Recent Bookings
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Bus</th>
                                    <th>Route</th>
                                    <th>Seat</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBookings as $i => $booking): ?>
                                    <tr>
                                        <td><?= $i+1 ?></td>
                                        <td><?= Html::encode($booking->user->username) ?></td>
                                        <td><?= Html::encode($booking->bus->type) ?> (<?= Html::encode($booking->bus->plate_number) ?>)</td>
                                        <td><?= Html::encode($booking->route->origin) ?> → <?= Html::encode($booking->route->destination) ?></td>
                                        <td><?= Html::encode($booking->seat->seat_number) ?></td>
                                        <td>
                                            <?php if ($booking->status == 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php elseif ($booking->status == 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= Html::encode($booking->status) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('Y-m-d H:i', $booking->created_at) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-lightning"></i> Quick Actions
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/booking/index'])) ?>" class="btn btn-success w-100">
                                <i class="bi bi-ticket-detailed"></i> Manage Bookings
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/bus/index'])) ?>" class="btn btn-primary w-100">
                                <i class="bi bi-bus-front"></i> Fleet Status
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/route/index'])) ?>" class="btn btn-info w-100">
                                <i class="bi bi-geo-alt"></i> Route Status
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-warning w-100">
                                <i class="bi bi-calendar-check"></i> Schedule
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div> 