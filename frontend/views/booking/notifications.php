<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Booking Notifications';
$this->params['breadcrumbs'][] = ['label' => 'My Bookings', 'url' => ['my-bookings']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-bell text-primary"></i> Booking Notifications
                </h1>
                <div>
                    <?= Html::a('<i class="bi bi-arrow-left"></i> Back to My Bookings', ['my-bookings'], ['class' => 'btn btn-outline-secondary']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Trips -->
    <?php if (!empty($upcomingTrips)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-event"></i> Upcoming Trips
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($upcomingTrips as $booking): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-warning h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="text-warning">
                                                        <i class="bi bi-bus-front"></i> <?= Html::encode($booking->bus->type) ?>
                                                    </h6>
                                                    <p class="mb-1">
                                                        <strong>Route:</strong> <?= Html::encode($booking->route->origin) ?> → <?= Html::encode($booking->route->destination) ?>
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Seat:</strong> <?= Html::encode($booking->seat->seat_number) ?>
                                                    </p>
                                                    <p class="mb-0">
                                                        <strong>Date:</strong> <?= date('M j, Y H:i', $booking->created_at) ?>
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-warning text-dark">Upcoming</span>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= date('M j', $booking->created_at) ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="<?= Url::to(['receipt', 'id' => $booking->id]) ?>" class="btn btn-outline-warning btn-sm">
                                                    <i class="bi bi-eye"></i> View Ticket
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recent Bookings -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i> Recent Bookings
                    </h5>
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

    <!-- Recent Cancellations -->
    <?php if (!empty($recentCancellations)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-x-circle"></i> Recent Cancellations
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($recentCancellations as $booking): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-danger h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="text-danger">
                                                        <i class="bi bi-bus-front"></i> <?= Html::encode($booking->bus->type) ?>
                                                    </h6>
                                                    <p class="mb-1">
                                                        <strong>Route:</strong> <?= Html::encode($booking->route->origin) ?> → <?= Html::encode($booking->route->destination) ?>
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Seat:</strong> <?= Html::encode($booking->seat->seat_number) ?>
                                                    </p>
                                                    <p class="mb-0">
                                                        <strong>Cancelled:</strong> <?= date('M j, Y H:i', $booking->updated_at) ?>
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-danger">Cancelled</span>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= date('M j', $booking->updated_at) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- No Notifications Message -->
    <?php if (empty($upcomingTrips) && empty($recentBookings) && empty($recentCancellations)): ?>
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="display-4 text-muted mb-3">
                        <i class="bi bi-bell-slash"></i>
                    </div>
                    <h3 class="text-muted mb-3">No Notifications</h3>
                    <p class="text-muted mb-4">You don't have any booking notifications at the moment.</p>
                    <a href="<?= Url::to(['my-bookings']) ?>" class="btn btn-primary">
                        <i class="bi bi-list-ul"></i> View All Bookings
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div> 