<?php
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $totalUsers int */
/* @var $totalBookings int */
/* @var $totalRevenue float */
/* @var $totalBuses int */
/* @var $totalRoutes int */
/* @var $monthlyRevenue array */
/* @var $topRoutes array */
/* @var $bookingTrends array */
/* @var $recentBookings common\models\Booking[] */

$this->title = 'Admin Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Bootstrap Icons
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
// Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => View::POS_END]);

$monthsJson = json_encode(array_keys($monthlyRevenue));
$revenueJson = json_encode(array_values($monthlyRevenue));
$trendDatesJson = json_encode(array_keys($bookingTrends));
$trendDataJson = json_encode(array_values($bookingTrends));

$js = <<<JS
// Monthly Revenue Chart
var ctx1 = document.getElementById('monthlyRevenueChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: $monthsJson,
        datasets: [{
            label: 'Monthly Revenue (TZS)',
            data: $revenueJson,
            fill: true,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            tension: 0.3
        }]
    },
    options: {scales: {y: {beginAtZero: true}}}
});

// Booking Trends Chart
var ctx2 = document.getElementById('bookingTrendsChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: $trendDatesJson,
        datasets: [{
            label: 'Daily Bookings',
            data: $trendDataJson,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2
        }]
    },
    options: {scales: {y: {beginAtZero: true}}, plugins: {legend: {display: false}}}
});
JS;
$this->registerJs($js);

$user = Yii::$app->user->identity;
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-primary sidebar py-4 shadow-sm rounded-end-4">
            <div class="sidebar-sticky">
                <div class="text-center mb-4">
                    <i class="bi bi-person-badge display-4 text-white"></i>
                    <h5 class="mt-2 mb-0 fw-bold text-white"><?= Html::encode($user->username) ?></h5>
                    <small class="text-light">Administrator</small>
                </div>
                <ul class="nav flex-column mb-4">
                    <li class="nav-item mb-2">
                        <a class="nav-link active fw-bold text-white" href="#"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/user/index'])) ?>"><i class="bi bi-people me-2"></i> User Management</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/bus/index'])) ?>"><i class="bi bi-bus-front me-2"></i> Fleet Management</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/route/index'])) ?>"><i class="bi bi-geo-alt me-2"></i> Route Management</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/booking/index'])) ?>"><i class="bi bi-ticket-detailed me-2"></i> Booking Management</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/admin'])) ?>"><i class="bi bi-graph-up me-2"></i> Reports</a>
                    </li>
                </ul>
                <div class="alert alert-info text-center small mb-0">
                    <i class="bi bi-info-circle"></i> Business Management Access
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="bi bi-person-badge text-primary"></i> Admin Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/admin'])) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-text"></i> View Reports
                        </a>
                        <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/admin'])) ?>" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print
                        </a>
                    </div>
                </div>
            </div>

            <!-- Business Overview Cards -->
            <div class="d-flex flex-wrap gap-3 mb-4">
                <div class="card text-white bg-primary mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-5"></i>
                        <h5 class="card-title mt-2">Customers</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $totalUsers ?></p>
                    </div>
                </div>
                <div class="card text-white bg-success mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-ticket-detailed display-5"></i>
                        <h5 class="card-title mt-2">Total Bookings</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $totalBookings ?></p>
                    </div>
                </div>
                <div class="card text-white bg-warning mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-coin display-5"></i>
                        <h5 class="card-title mt-2">Total Revenue</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= number_format($totalRevenue) ?> TZS</p>
                    </div>
                </div>
                <div class="card text-white bg-info mb-3 flex-fill" style="min-width: 180px;">
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

            <!-- Ticket Verification Statistics -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark fw-bold">
                            <i class="bi bi-qr-code-scan"></i> Ticket Verification Status
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                $activeTickets = \common\models\Booking::find()->where(['ticket_status' => 'active'])->count();
                                $usedTickets = \common\models\Booking::find()->where(['ticket_status' => 'used'])->count();
                                $expiredTickets = \common\models\Booking::find()->where(['ticket_status' => 'expired'])->count();
                                $totalTickets = $activeTickets + $usedTickets + $expiredTickets;
                                $verificationRate = $totalTickets > 0 ? round(($usedTickets / $totalTickets) * 100, 1) : 0;
                                ?>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="border rounded p-3">
                                        <i class="bi bi-check-circle text-success display-4"></i>
                                        <h4 class="mt-2 mb-1"><?= $activeTickets ?></h4>
                                        <small class="text-muted">Active Tickets</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="border rounded p-3">
                                        <i class="bi bi-x-circle text-danger display-4"></i>
                                        <h4 class="mt-2 mb-1"><?= $usedTickets ?></h4>
                                        <small class="text-muted">Verified/Used</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="border rounded p-3">
                                        <i class="bi bi-exclamation-triangle text-warning display-4"></i>
                                        <h4 class="mt-2 mb-1"><?= $expiredTickets ?></h4>
                                        <small class="text-muted">Expired</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="border rounded p-3">
                                        <i class="bi bi-percent text-info display-4"></i>
                                        <h4 class="mt-2 mb-1"><?= $verificationRate ?>%</h4>
                                        <small class="text-muted">Verification Rate</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" style="width: <?= $activeTickets > 0 ? ($activeTickets / $totalTickets) * 100 : 0 ?>%">
                                        Active (<?= $activeTickets ?>)
                                    </div>
                                    <div class="progress-bar bg-danger" style="width: <?= $usedTickets > 0 ? ($usedTickets / $totalTickets) * 100 : 0 ?>%">
                                        Used (<?= $usedTickets ?>)
                                    </div>
                                    <div class="progress-bar bg-warning" style="width: <?= $expiredTickets > 0 ? ($expiredTickets / $totalTickets) * 100 : 0 ?>%">
                                        Expired (<?= $expiredTickets ?>)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue and Trends -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white fw-bold">
                            <i class="bi bi-graph-up"></i> Business Analytics
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <h6>Monthly Revenue Trend</h6>
                                    <canvas id="monthlyRevenueChart" height="120"></canvas>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <h6>Daily Booking Trends</h6>
                                    <canvas id="bookingTrendsChart" height="120"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="bi bi-trophy"></i> Top Performing Routes
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <?php foreach ($topRoutes as $i => $route): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= Html::encode($route['origin']) ?> → <?= Html::encode($route['destination']) ?></strong>
                                            <br><small class="text-muted"><?= $route['booking_count'] ?> bookings</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill"><?= number_format($route['total_revenue']) ?> TZS</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white fw-bold">
                            <i class="bi bi-bell"></i> Business Alerts
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Revenue target on track</li>
                                <li class="mb-2"><i class="bi bi-exclamation-circle-fill text-warning"></i> 3 routes need attention</li>
                                <li class="mb-2"><i class="bi bi-info-circle-fill text-info"></i> Peak season approaching</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Customer satisfaction: 4.8/5</li>
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
                                    <th>Amount</th>
                                    <th>Ticket Status</th>
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
                                        <td><?= number_format($booking->route->price) ?> TZS</td>
                                        <td>
                                            <?php
                                            $statusLabels = [
                                                'active' => '<span class="badge bg-success">🟢 Active</span>',
                                                'used' => '<span class="badge bg-danger">🔴 Used</span>',
                                                'expired' => '<span class="badge bg-warning">🟡 Expired</span>'
                                            ];
                                            echo $statusLabels[$booking->ticket_status] ?? $booking->ticket_status;
                                            ?>
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
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-lightning"></i> Quick Actions
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/booking/index'])) ?>" class="btn btn-success w-100">
                                <i class="bi bi-ticket-detailed"></i> View All Bookings
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/route/index'])) ?>" class="btn btn-info w-100">
                                <i class="bi bi-geo-alt"></i> Manage Routes
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/bus/index'])) ?>" class="btn btn-warning w-100">
                                <i class="bi bi-bus-front"></i> Fleet Status
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/admin'])) ?>" class="btn btn-secondary w-100">
                                <i class="bi bi-file-earmark-text"></i> Business Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div> 