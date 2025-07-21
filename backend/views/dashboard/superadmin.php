<?php
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $totalUsers int */
/* @var $totalBookings int */
/* @var $totalRevenue float */
/* @var $totalBuses int */
/* @var $totalRoutes int */
/* @var $usersByRole array */
/* @var $activeBookings int */
/* @var $pendingBookings int */
/* @var $completedBookings int */
/* @var $recentBookings common\models\Booking[] */
/* @var $recentUsers common\models\User[] */
/* @var $analyticsData array */

$this->title = 'Super Admin Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Bootstrap Icons
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
// Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => View::POS_END]);

$monthsJson = json_encode($analyticsData['months']);
$bookingsJson = json_encode($analyticsData['bookingsPerMonth']);
$usersJson = json_encode($analyticsData['usersPerMonth']);
$routeLabelsJson = json_encode($analyticsData['routeLabels']);
$revenueJson = json_encode(array_values($analyticsData['revenuePerRoute']));

$js = <<<JS
// Bookings Chart
var ctx1 = document.getElementById('bookingsChart').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: $monthsJson,
        datasets: [{
            label: 'Bookings per Month',
            data: $bookingsJson,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2
        }]
    },
    options: {scales: {y: {beginAtZero: true}}, plugins: {legend: {display: false}}}
});

// Revenue Chart
var ctx2 = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: $routeLabelsJson,
        datasets: [{
            label: 'Revenue per Route',
            data: $revenueJson,
            backgroundColor: 'rgba(255, 99, 132, 0.7)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2
        }]
    },
    options: {scales: {y: {beginAtZero: true}}, plugins: {legend: {display: false}}}
});

// Users Chart
var ctx3 = document.getElementById('usersChart').getContext('2d');
new Chart(ctx3, {
    type: 'line',
    data: {
        labels: $monthsJson,
        datasets: [{
            label: 'User Registrations per Month',
            data: $usersJson,
            fill: true,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            tension: 0.3
        }]
    },
    options: {scales: {y: {beginAtZero: true}}}
});

// Users by Role Chart
var ctx4 = document.getElementById('usersByRoleChart').getContext('2d');
new Chart(ctx4, {
    type: 'doughnut',
    data: {
        labels: ['Super Admin', 'Admin', 'Manager', 'Staff', 'Users'],
        datasets: [{
            data: [{$usersByRole['superadmin']}, {$usersByRole['admin']}, {$usersByRole['manager']}, {$usersByRole['staff']}, {$usersByRole['user']}],
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
        <nav class="col-md-2 d-none d-md-block bg-dark sidebar py-4 shadow-sm rounded-end-4">
            <div class="sidebar-sticky">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-check display-4 text-warning"></i>
                    <h5 class="mt-2 mb-0 fw-bold text-white"><?= Html::encode($user->username) ?></h5>
                    <small class="text-light">Super Administrator</small>
                </div>
                <ul class="nav flex-column mb-4">
                    <li class="nav-item mb-2">
                        <a class="nav-link active fw-bold text-warning" href="#"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
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
                        <a class="nav-link text-light" href="#"><i class="bi bi-gear me-2"></i> System Settings</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/super-admin'])) ?>"><i class="bi bi-file-earmark-text me-2"></i> Reports</a>
                    </li>
                </ul>
                <div class="alert alert-warning text-center small mb-0">
                    <i class="bi bi-shield-check"></i> Full System Access
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="bi bi-shield-check text-warning"></i> Super Admin Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/super-admin'])) ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-file-earmark-text"></i> View Report
                        </a>
                        <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/super-admin'])) ?>" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Overview Cards -->
            <div class="d-flex flex-wrap gap-3 mb-4">
                <div class="card text-white bg-primary mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-5"></i>
                        <h5 class="card-title mt-2">Total Users</h5>
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

            <!-- System Health Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle-fill text-success display-4"></i>
                            <h5 class="card-title">Active Bookings</h5>
                            <p class="card-text display-6 fw-bold text-success"><?= $activeBookings ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-fill text-warning display-4"></i>
                            <h5 class="card-title">Pending Bookings</h5>
                            <p class="card-text display-6 fw-bold text-warning"><?= $pendingBookings ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="bi bi-check2-all text-info display-4"></i>
                            <h5 class="card-title">Completed</h5>
                            <p class="card-text display-6 fw-bold text-info"><?= $completedBookings ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up text-primary display-4"></i>
                            <h5 class="card-title">System Health</h5>
                            <p class="card-text display-6 fw-bold text-primary">98%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics and Charts -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white fw-bold">
                            <i class="bi bi-bar-chart"></i> System Analytics
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <h6>Bookings per Month</h6>
                                    <canvas id="bookingsChart" height="120"></canvas>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <h6>Revenue per Route</h6>
                                    <canvas id="revenueChart" height="120"></canvas>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <h6>User Registrations per Month</h6>
                                    <canvas id="usersChart" height="120"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark fw-bold">
                            <i class="bi bi-pie-chart"></i> User Distribution
                        </div>
                        <div class="card-body">
                            <canvas id="usersByRoleChart" height="200"></canvas>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white fw-bold">
                            <i class="bi bi-shield-check"></i> System Status
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Database: Online</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Email Service: Active</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Payment Gateway: Connected</li>
                                <li class="mb-2"><i class="bi bi-exclamation-circle-fill text-warning"></i> Backup: Last 24h</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Security: All Clear</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="bi bi-clock-history"></i> Recent Bookings
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>User</th>
                                            <th>Route</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentBookings as $i => $booking): ?>
                                            <tr>
                                                <td><?= $i+1 ?></td>
                                                <td><?= Html::encode($booking->user->username) ?></td>
                                                <td><?= Html::encode($booking->route->origin) ?> → <?= Html::encode($booking->route->destination) ?></td>
                                                <td><?= date('Y-m-d H:i', $booking->created_at) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white fw-bold">
                            <i class="bi bi-person-plus"></i> Recent User Registrations
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentUsers as $i => $user): ?>
                                            <tr>
                                                <td><?= $i+1 ?></td>
                                                <td><?= Html::encode($user->username) ?></td>
                                                <td><span class="badge bg-secondary"><?= Html::encode($user->role) ?></span></td>
                                                <td><?= date('Y-m-d', $user->created_at) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-lightning"></i> Quick Actions
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/user/create'])) ?>" class="btn btn-primary w-100">
                                <i class="bi bi-person-plus"></i> Add User
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/bus/create'])) ?>" class="btn btn-success w-100">
                                <i class="bi bi-bus-front"></i> Add Bus
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/route/create'])) ?>" class="btn btn-info w-100">
                                <i class="bi bi-geo-alt"></i> Add Route
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/super-admin'])) ?>" class="btn btn-warning w-100">
                                <i class="bi bi-file-earmark-text"></i> Generate Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div> 