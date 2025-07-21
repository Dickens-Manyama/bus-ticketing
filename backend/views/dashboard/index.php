<?php
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $totalUsers int */
/* @var $totalBookings int */
/* @var $totalRevenue float */
/* @var $totalBuses int */
/* @var $totalRoutes int */
/* @var $recentBookings common\models\Booking[] */
/* @var $months array */
/* @var $bookingsPerMonth array */
/* @var $revenuePerRoute array */
/* @var $usersPerMonth array */
/* @var $routeLabels array */

$this->title = 'Admin Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Bootstrap Icons
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
// Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => View::POS_END]);
$monthsJson = json_encode($months);
$bookingsJson = json_encode($bookingsPerMonth);
$usersJson = json_encode($usersPerMonth);
$routeLabelsJson = json_encode($routeLabels);
$revenueJson = json_encode(array_values($revenuePerRoute));
$js = <<<JS
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
JS;
$this->registerJs($js);
$user = Yii::$app->user->identity;
?>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar py-4 shadow-sm rounded-end-4">
            <div class="sidebar-sticky">
                <div class="text-center mb-4">
                    <i class="bi bi-person-badge display-4 text-primary"></i>
                    <h5 class="mt-2 mb-0 fw-bold"><?= Html::encode($user->username) ?></h5>
                    <small class="text-muted text-lowercase">(<?= Html::encode($user->role) ?>)</small>
                </div>
                <ul class="nav flex-column mb-4">
                    <li class="nav-item mb-2">
                        <a class="nav-link active fw-bold" href="#"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/user/index'])) ?>"><i class="bi bi-people me-2"></i> Users</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/bus/index'])) ?>"><i class="bi bi-bus-front me-2"></i> Buses</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/route/index'])) ?>"><i class="bi bi-geo-alt me-2"></i> Routes</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/booking/index'])) ?>"><i class="bi bi-ticket-detailed me-2"></i> Bookings</a>
                    </li>
                </ul>
                <div class="alert alert-info text-center small mb-0">
                    <i class="bi bi-info-circle"></i> Welcome, <?= Html::encode($user->username) ?>!
                </div>
            </div>
        </nav>
        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <!-- Welcome Message -->
            <div class="alert alert-primary border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle display-6 me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-1">Welcome back, <?= Html::encode($user->username) ?>!</h4>
                        <p class="mb-0">You are logged in as <strong><?= Html::encode(ucfirst($user->role)) ?></strong>. 
                        Last login: <?= $user->last_login ? date('M j, Y \a\t g:i A', $user->last_login) : 'First time login' ?></p>
                    </div>
                </div>
            </div>
            
            <div class="d-flex flex-wrap gap-3 mb-4">
                <div class="card text-white bg-primary mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-5"></i>
                        <h5 class="card-title mt-2">Users</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $totalUsers ?></p>
                    </div>
                </div>
                <div class="card text-white bg-success mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-ticket-detailed display-5"></i>
                        <h5 class="card-title mt-2">Bookings</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $totalBookings ?></p>
                    </div>
                </div>
                <div class="card text-white bg-warning mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-coin display-5"></i>
                        <h5 class="card-title mt-2">Revenue</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= number_format($totalRevenue) ?> TZS</p>
                    </div>
                </div>
                <div class="card text-white bg-info mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-bus-front display-5"></i>
                        <h5 class="card-title mt-2">Buses</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $totalBuses ?></p>
                    </div>
                </div>
                <div class="card text-white bg-secondary mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-geo-alt display-5"></i>
                        <h5 class="card-title mt-2">Routes</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $totalRoutes ?></p>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white fw-bold"><i class="bi bi-bar-chart"></i> Analytics</div>
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
                        <div class="card-header bg-info text-white fw-bold"><i class="bi bi-bell"></i> Notifications</div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><i class="bi bi-check-circle-fill text-success"></i> System running smoothly</li>
                                <li><i class="bi bi-exclamation-circle-fill text-warning"></i> No pending approvals</li>
                                <li><i class="bi bi-envelope-fill text-primary"></i> 0 new messages</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white fw-bold"><i class="bi bi-person"></i> Profile</div>
                        <div class="card-body text-center">
                            <i class="bi bi-person-circle display-4 text-secondary"></i>
                            <h5 class="mt-2 mb-0 fw-bold"><?= Html::encode($user->username) ?></h5>
                            <small class="text-muted">Role: <?= Html::encode($user->role) ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white fw-bold"><i class="bi bi-clock-history"></i> Recent Bookings</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Bus</th>
                                    <th>Route</th>
                                    <th>Seat</th>
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
                                        <td><?= date('Y-m-d H:i', $booking->created_at) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div> 