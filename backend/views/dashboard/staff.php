<?php
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $totalBookings int */
/* @var $todayBookings int */
/* @var $pendingBookings int */
/* @var $totalBuses int */
/* @var $totalRoutes int */
/* @var $recentBookings common\models\Booking[] */
/* @var $availableBuses int */
/* @var $activeRoutes int */

$this->title = 'Staff Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Bootstrap Icons
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');

$user = Yii::$app->user->identity;
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-secondary sidebar py-4 shadow-sm rounded-end-4">
            <div class="sidebar-sticky">
                <div class="text-center mb-4">
                    <i class="bi bi-person display-4 text-white"></i>
                    <h5 class="mt-2 mb-0 fw-bold text-white"><?= Html::encode($user->username) ?></h5>
                    <small class="text-light">Staff Member</small>
                </div>
                <ul class="nav flex-column mb-4">
                    <li class="nav-item mb-2">
                        <a class="nav-link active fw-bold text-white" href="#"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/booking/index'])) ?>"><i class="bi bi-ticket-detailed me-2"></i> Bookings</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/bus/index'])) ?>"><i class="bi bi-bus-front me-2"></i> Buses</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/route/index'])) ?>"><i class="bi bi-geo-alt me-2"></i> Routes</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="#"><i class="bi bi-calendar me-2"></i> Schedule</a>
                    </li>
                </ul>
                <div class="alert alert-secondary text-center small mb-0">
                    <i class="bi bi-info-circle"></i> Basic Operations Access
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="bi bi-person text-secondary"></i> Staff Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Today's Tasks</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Help</button>
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="alert alert-info mb-4">
                <h5><i class="bi bi-info-circle"></i> Welcome, <?= Html::encode($user->username) ?>!</h5>
                <p class="mb-0">Here's your daily overview. You have <strong><?= $pendingBookings ?></strong> pending bookings to process.</p>
            </div>

            <!-- Basic Overview Cards -->
            <div class="d-flex flex-wrap gap-3 mb-4">
                <div class="card text-white bg-primary mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-ticket-detailed display-5"></i>
                        <h5 class="card-title mt-2">Total Bookings</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $totalBookings ?></p>
                    </div>
                </div>
                <div class="card text-white bg-success mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-day display-5"></i>
                        <h5 class="card-title mt-2">Today's Bookings</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $todayBookings ?></p>
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
                        <i class="bi bi-bus-front display-5"></i>
                        <h5 class="card-title mt-2">Available Buses</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $availableBuses ?></p>
                    </div>
                </div>
                <div class="card text-white bg-secondary mb-3 flex-fill" style="min-width: 180px;">
                    <div class="card-body text-center">
                        <i class="bi bi-geo-alt display-5"></i>
                        <h5 class="card-title mt-2">Active Routes</h5>
                        <p class="card-text display-6 fw-bold mb-0"><?= $activeRoutes ?></p>
                    </div>
                </div>
            </div>

            <!-- Daily Tasks and Status -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white fw-bold">
                            <i class="bi bi-list-check"></i> Today's Tasks
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="bi bi-check-circle-fill text-success"></i> Completed</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check text-success"></i> Morning shift started</li>
                                        <li><i class="bi bi-check text-success"></i> System check completed</li>
                                        <li><i class="bi bi-check text-success"></i> First bookings processed</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="bi bi-clock-fill text-warning"></i> Pending</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-clock text-warning"></i> Process <?= $pendingBookings ?> bookings</li>
                                        <li><i class="bi bi-clock text-warning"></i> Update bus status</li>
                                        <li><i class="bi bi-clock text-warning"></i> End of day report</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white fw-bold">
                            <i class="bi bi-bell"></i> Notifications
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> System running normally</li>
                                <li class="mb-2"><i class="bi bi-info-circle-fill text-info"></i> <?= $availableBuses ?> buses available</li>
                                <li class="mb-2"><i class="bi bi-exclamation-circle-fill text-warning"></i> <?= $pendingBookings ?> bookings need attention</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> All routes active</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="bi bi-person"></i> Your Profile
                        </div>
                        <div class="card-body text-center">
                            <i class="bi bi-person-circle display-4 text-secondary"></i>
                            <h5 class="mt-2 mb-0 fw-bold"><?= Html::encode($user->username) ?></h5>
                            <small class="text-muted">Staff Member</small>
                            <hr>
                            <p class="mb-0"><strong>Shift:</strong> Day Shift</p>
                            <p class="mb-0"><strong>Status:</strong> Active</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="bi bi-clock-history"></i> Recent Bookings
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Route</th>
                                    <th>Seat</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBookings as $i => $booking): ?>
                                    <tr>
                                        <td><?= $i+1 ?></td>
                                        <td><?= Html::encode($booking->user->username) ?></td>
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
                                        <td><?= date('H:i', $booking->created_at) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="bi bi-lightning"></i> Quick Actions
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/booking/index'])) ?>" class="btn btn-primary w-100">
                                <i class="bi bi-ticket-detailed"></i> View Bookings
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/booking/create'])) ?>" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle"></i> New Booking
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/bus/index'])) ?>" class="btn btn-info w-100">
                                <i class="bi bi-bus-front"></i> Check Buses
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-warning w-100">
                                <i class="bi bi-question-circle"></i> Help
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips Section -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="bi bi-lightbulb"></i> Daily Tips
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bi bi-clock-history text-primary display-4"></i>
                                <h6>Process bookings promptly</h6>
                                <small class="text-muted">Keep customers happy with quick service</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bi bi-check-circle text-success display-4"></i>
                                <h6>Double-check details</h6>
                                <small class="text-muted">Accuracy is important for customer satisfaction</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bi bi-people text-info display-4"></i>
                                <h6>Be helpful to customers</h6>
                                <small class="text-muted">Good service builds customer loyalty</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div> 