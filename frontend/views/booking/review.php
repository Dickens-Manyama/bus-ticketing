<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $bus common\models\Bus */
/* @var $route common\models\Route */
/* @var $seat common\models\Seat */
/* @var $basePrice float */
/* @var $finalPrice float */

$this->title = 'Review Booking';
$this->params['breadcrumbs'][] = ['label' => 'Book Ticket', 'url' => ['bus']];
$this->params['breadcrumbs'][] = ['label' => 'Select Bus', 'url' => ['route', 'bus_id' => $bus->id]];
$this->params['breadcrumbs'][] = ['label' => 'Select Route', 'url' => ['seat', 'bus_id' => $bus->id, 'route_id' => $route->id]];
$this->params['breadcrumbs'][] = ['label' => 'Select Seat', 'url' => ['seat', 'bus_id' => $bus->id, 'route_id' => $route->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-4">
                <h1 class="h2 mb-3">
                    <i class="bi bi-clipboard-check text-primary"></i> Review Your Booking
                </h1>
                <p class="text-muted">Please review your booking details before proceeding to payment</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Passenger Details -->
        <div class="col-md-6 mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Passenger Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Full Name</small>
                            <p class="mb-1 fw-bold"><?= Html::encode(Yii::$app->user->identity->username) ?></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Email</small>
                            <p class="mb-1 fw-bold"><?= Html::encode(Yii::$app->user->identity->email) ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">User ID</small>
                            <p class="mb-1 fw-bold">#<?= Html::encode(Yii::$app->user->identity->id) ?></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Account Status</small>
                            <p class="mb-1">
                                <span class="badge bg-success">Active</span>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted">Member Since</small>
                            <p class="mb-1 fw-bold"><?= date('d M Y', Yii::$app->user->identity->created_at) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Journey Details -->
        <div class="col-md-6 mb-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Journey Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">From</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($route->origin) ?></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">To</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($route->destination) ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Distance</small>
                            <!-- Removed distance display: property does not exist -->
                            <p class="mb-1 fw-bold">N/A</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Duration</small>
                            <!-- Removed duration display: property does not exist -->
                            <p class="mb-1 fw-bold">N/A</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Travel Date</small>
                            <p class="mb-1 fw-bold"><?= date('d M Y', strtotime($route->departure_time)) ?></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Departure Time</small>
                            <p class="mb-1 fw-bold"><?= date('H:i', strtotime($route->departure_time)) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Bus & Seat Details -->
        <div class="col-md-6 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bus-front"></i> Bus & Seat Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Bus Type</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($bus->type) ?></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Plate Number</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($bus->plate_number) ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Bus Class</small>
                            <p class="mb-1">
                                <span class="badge bg-<?= $bus->getClassColor() ?>"><?= $bus->getClassLabel() ?></span>
                            </p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Seating Layout</small>
                            <p class="mb-1 fw-bold"><?= $bus->getSeatingConfigLabel() ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Selected Seat</small>
                            <p class="mb-1 fw-bold text-primary">Seat <?= Html::encode($seat->seat_number) ?></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Seat Type</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($seat->type ?? 'Standard') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Details -->
        <div class="col-md-6 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-calculator"></i> Pricing Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-8">
                            <small class="text-muted">Route</small>
                            <p class="mb-1"><?= Html::encode($route->origin) ?> → <?= Html::encode($route->destination) ?></p>
                        </div>
                        <div class="col-4 text-end">
                            <p class="mb-1 fw-bold"><?= number_format($basePrice) ?> TZS</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-8">
                            <small class="text-muted">Bus</small>
                            <p class="mb-1"><?= Html::encode($bus->type) ?> (<?= Html::encode($bus->plate_number) ?>)</p>
                        </div>
                        <div class="col-4 text-end">
                            <p class="mb-1 text-muted"><?= $bus->getClassLabel() ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-8">
                            <h6 class="mb-0 fw-bold">Total Amount</h6>
                        </div>
                        <div class="col-4 text-end">
                            <h5 class="mb-0 fw-bold text-success"><?= number_format($finalPrice) ?> TZS</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="d-flex justify-content-center gap-3">
                <a href="<?= Url::to(['seat', 'bus_id' => $bus->id, 'route_id' => $route->id]) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Seat Selection
                </a>
                <a href="<?= Url::to(['payment', 'bus_id' => $bus->id, 'route_id' => $route->id, 'seat_id' => $seat->id]) ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-credit-card"></i> Proceed to Payment
                </a>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-light">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle"></i> Important Information</h6>
                    <ul class="list-unstyled mb-0">
                        <li><i class="bi bi-check-circle text-success me-2"></i> Please arrive at least 30 minutes before departure time</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i> Bring a valid ID for verification</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i> Cancellation is allowed up to 2 hours before departure</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i> Seat changes are not allowed after booking confirmation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sticky-top {
    z-index: 1020;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style> 