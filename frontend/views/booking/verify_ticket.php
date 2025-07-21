<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\Alert;

/* @var $this yii\web\View */
/* @var $booking common\models\Booking */
/* @var $status string */
/* @var $message string */

$this->title = 'Verify Ticket #' . $booking->id;
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>
                        Ticket Verification
                    </h2>
                    <p class="mb-0 mt-2">Ticket #<?= $booking->id ?></p>
                </div>
                
                <div class="card-body p-4">
                    <!-- Status Alert -->
                    <?php if ($status === 'active'): ?>
                        <?= Alert::widget([
                            'options' => ['class' => 'alert-success'],
                            'body' => '<i class="fas fa-check-circle me-2"></i>' . $message,
                        ]) ?>
                    <?php elseif ($status === 'used'): ?>
                        <?= Alert::widget([
                            'options' => ['class' => 'alert-danger'],
                            'body' => '<i class="fas fa-times-circle me-2"></i>' . $message,
                        ]) ?>
                    <?php elseif ($status === 'expired'): ?>
                        <?= Alert::widget([
                            'options' => ['class' => 'alert-warning'],
                            'body' => '<i class="fas fa-exclamation-triangle me-2"></i>' . $message,
                        ]) ?>
                    <?php elseif ($status === 'verified'): ?>
                        <?= Alert::widget([
                            'options' => ['class' => 'alert-success'],
                            'body' => '<i class="fas fa-check-circle me-2"></i>' . $message,
                        ]) ?>
                    <?php endif; ?>
                    
                    <!-- Ticket Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-primary mb-3">
                                <i class="fas fa-user me-2"></i>Passenger Information
                            </h4>
                            <?= DetailView::widget([
                                'model' => $booking->user,
                                'attributes' => [
                                    'username:raw:Name',
                                    'email:raw:Email',
                                    'id:raw:User ID',
                                ],
                                'options' => ['class' => 'table table-borderless'],
                            ]) ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h4 class="text-primary mb-3">
                                <i class="fas fa-route me-2"></i>Journey Details
                            </h4>
                            <?= DetailView::widget([
                                'model' => $booking,
                                'attributes' => [
                                    [
                                        'attribute' => 'bus.type',
                                        'label' => 'Bus Type',
                                        'value' => $booking->bus->type,
                                    ],
                                    [
                                        'attribute' => 'bus.plate_number',
                                        'label' => 'Plate Number',
                                        'value' => $booking->bus->plate_number,
                                    ],
                                    [
                                        'attribute' => 'route',
                                        'label' => 'Route',
                                        'value' => $booking->route->origin . ' → ' . $booking->route->destination,
                                    ],
                                    [
                                        'attribute' => 'seat.seat_number',
                                        'label' => 'Seat Number',
                                        'value' => $booking->seat->seat_number,
                                    ],
                                    [
                                        'attribute' => 'route.price',
                                        'label' => 'Price',
                                        'value' => number_format($booking->route->price) . ' TZS',
                                    ],
                                ],
                                'options' => ['class' => 'table table-borderless'],
                            ]) ?>
                        </div>
                    </div>
                    
                    <!-- Payment Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="text-primary mb-3">
                                <i class="fas fa-credit-card me-2"></i>Payment Information
                            </h4>
                            <?= DetailView::widget([
                                'model' => $booking,
                                'attributes' => [
                                    'payment_method:raw:Payment Method',
                                    'payment_status:raw:Payment Status',
                                    'status:raw:Booking Status',
                                    'ticket_status:raw:Ticket Status',
                                    [
                                        'attribute' => 'created_at',
                                        'label' => 'Booked On',
                                        'value' => date('F j, Y \a\t g:i A', $booking->created_at),
                                    ],
                                ],
                                'options' => ['class' => 'table table-borderless'],
                            ]) ?>
                        </div>
                    </div>
                    
                    <!-- Scanning Information (if ticket is used) -->
                    <?php if ($booking->isUsed() && $booking->scanned_at): ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-history me-2"></i>Scanning Information
                                </h4>
                                <?= DetailView::widget([
                                    'model' => $booking,
                                    'attributes' => [
                                        [
                                            'attribute' => 'scanned_at',
                                            'label' => 'Scanned At',
                                            'value' => '<span class="badge bg-primary">🔵 Scanned</span><br><small>' . date('F j, Y \a\t g:i A', $booking->scanned_at) . '</small>',
                                            'format' => 'raw',
                                        ],
                                        [
                                            'attribute' => 'scannedBy.username',
                                            'label' => 'Scanned By',
                                            'value' => function($model) {
                                                if ($model->scannedBy) {
                                                    return '<span class="badge bg-primary">🔵 Scanned</span><br><small>' . $model->scannedBy->username . '</small>';
                                                } else {
                                                    return '<span class="badge bg-info">📱 Scanned by Phone</span><br><small>Mobile QR Verification</small>';
                                                }
                                            },
                                            'format' => 'raw',
                                        ],
                                    ],
                                    'options' => ['class' => 'table table-borderless'],
                                ]) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Verification Actions -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <?php if ($booking->isActive()): ?>
                                <form method="post" class="d-inline">
                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                    <button type="submit" class="btn btn-success btn-lg px-5 py-3" 
                                            onclick="return confirm('Are you sure you want to verify this ticket and allow boarding?')">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Verify & Allow Boarding
                                    </button>
                                </form>
                            <?php elseif ($booking->isUsed()): ?>
                                <button class="btn btn-secondary btn-lg px-5 py-3" disabled>
                                    <i class="fas fa-times-circle me-2"></i>
                                    Ticket Already Used
                                </button>
                            <?php elseif ($booking->isExpired()): ?>
                                <button class="btn btn-warning btn-lg px-5 py-3" disabled>
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Ticket Expired
                                </button>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <a href="<?= Yii::$app->request->referrer ?: ['site/index'] ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.card {
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.btn-lg {
    border-radius: 25px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.table-borderless td {
    border: none;
    padding: 8px 0;
}

.text-primary {
    color: #667eea !important;
}
</style> 