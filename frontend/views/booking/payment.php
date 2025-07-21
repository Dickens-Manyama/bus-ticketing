<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $bus common\models\Bus */
/* @var $route common\models\Route */
/* @var $seat common\models\Seat */
/* @var $basePrice float */
/* @var $finalPrice float */
/* @var $paymentMethods array */

$this->title = 'Select Payment Method';
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
                    <i class="bi bi-credit-card text-primary"></i> Select Payment Method
                </h1>
                <p class="text-muted">Choose your preferred payment method to complete your booking</p>
            </div>
        </div>
    </div>

    <!-- Booking Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Booking Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Bus</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($bus->type) ?></p>
                            <small class="text-muted"><?= Html::encode($bus->plate_number) ?></small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Route</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($route->origin) ?> → <?= Html::encode($route->destination) ?></p>
                            <small class="text-muted">
                                <?php if (!empty($route->distance)): ?>
                                    <?= Html::encode($route->distance) ?> km
                                <?php endif; ?>
                                <?php if (!empty($route->departure_time)): ?>
                                    | Departs at <?= date('H:i', strtotime($route->departure_time)) ?>
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Seat</small>
                            <p class="mb-1 fw-bold">Seat <?= Html::encode($seat->seat_number) ?></p>
                            <small class="text-muted"><?= $bus->getClassLabel() ?></small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Total Amount</small>
                            <p class="mb-1 fw-bold text-success fs-5"><?= number_format($finalPrice) ?> TZS</p>
                            <small class="text-muted">Base: <?= number_format($basePrice) ?> TZS</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-bank"></i> Bank Payments</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($paymentMethods['banks'] as $method => $details): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-<?= $details['color'] ?> payment-method-card">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi <?= $details['icon'] ?> fs-1 text-<?= $details['color'] ?>"></i>
                                        </div>
                                        <h5 class="card-title"><?= Html::encode($details['name']) ?></h5>
                                        <p class="card-text text-muted"><?= Html::encode($details['description']) ?></p>
                                        <div class="d-grid">
                                            <?= Html::a(
                                                '<i class="bi bi-arrow-right"></i> Pay with ' . $details['name'],
                                                ['pay', 'bus_id' => $bus->id, 'route_id' => $route->id, 'seat_id' => $seat->id, 'payment_method' => $method],
                                                [
                                                    'class' => 'btn btn-outline-' . $details['color'],
                                                    'data' => [
                                                        'confirm' => 'Are you sure you want to pay ' . number_format($finalPrice) . ' TZS via ' . $details['name'] . '?',
                                                        'method' => 'post',
                                                    ]
                                                ]
                                            ) ?>
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

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-phone"></i> Mobile Money Payments</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($paymentMethods['mobile_money'] as $method => $details): ?>
                            <div class="col-md-6 col-lg-3 mb-3">
                                <div class="card h-100 border-<?= $details['color'] ?> payment-method-card">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi <?= $details['icon'] ?> fs-1 text-<?= $details['color'] ?>"></i>
                                        </div>
                                        <h6 class="card-title"><?= Html::encode($details['name']) ?></h6>
                                        <p class="card-text text-muted small"><?= Html::encode($details['description']) ?></p>
                                        <div class="d-grid">
                                            <?= Html::a(
                                                '<i class="bi bi-arrow-right"></i> Pay with ' . $details['name'],
                                                ['pay', 'bus_id' => $bus->id, 'route_id' => $route->id, 'seat_id' => $seat->id, 'payment_method' => $method],
                                                [
                                                    'class' => 'btn btn-outline-' . $details['color'] . ' btn-sm',
                                                    'data' => [
                                                        'confirm' => 'Are you sure you want to pay ' . number_format($finalPrice) . ' TZS via ' . $details['name'] . '?',
                                                        'method' => 'post',
                                                    ]
                                                ]
                                            ) ?>
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

    <!-- Back Button -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="<?= Url::to(['seat', 'bus_id' => $bus->id, 'route_id' => $route->id]) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Seat Selection
            </a>
        </div>
    </div>
</div>

<style>
.payment-method-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-method-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.payment-method-card .card-body {
    padding: 1.5rem;
}

.payment-method-card i {
    transition: all 0.3s ease;
}

.payment-method-card:hover i {
    transform: scale(1.1);
}
</style> 