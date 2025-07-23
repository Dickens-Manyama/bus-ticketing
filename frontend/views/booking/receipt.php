<?php
// (CSP header removed from view. Set globally in your Yii2 config or web server for best results.)
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $booking common\models\Booking */
/* @var $qrImageData string */

$this->title = 'Booking Receipt';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <span class="display-4 text-success"><i class="bi bi-check-circle"></i></span>
                        <h2 class="fw-bold mb-2">Payment Successful!</h2>
                        <p class="text-muted mb-0">Your ticket has been booked successfully.</p>
                    </div>
                    
                    <!-- Booking Details -->
                    <div class="receipt-details mb-4">
                        <h4 class="text-primary mb-3"><i class="bi bi-ticket-perforated"></i> Booking Details</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="bi bi-bus-front text-primary"></i> <strong>Bus:</strong></span>
                                        <span><?= Html::encode($booking->bus->type) ?> (<?= Html::encode($booking->bus->plate_number) ?>)</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="bi bi-geo-alt text-primary"></i> <strong>Route:</strong></span>
                                        <span><?= Html::encode($booking->route->origin) ?> → <?= Html::encode($booking->route->destination) ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="bi bi-person-seat text-primary"></i> <strong>Seat:</strong></span>
                                        <span><?= Html::encode($booking->seat->seat_number) ?></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="bi bi-cash-coin text-primary"></i> <strong>Price:</strong></span>
                                        <span class="fw-bold text-success"><?= number_format($booking->route->price) ?> TZS</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="bi bi-person text-primary"></i> <strong>Passenger:</strong></span>
                                        <span><?= Html::encode($booking->user->username) ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="bi bi-calendar text-primary"></i> <strong>Date:</strong></span>
                                        <span><?= date('Y-m-d H:i', $booking->created_at) ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Information -->
                    <div class="payment-info mb-4">
                        <h5 class="text-success mb-2"><i class="bi bi-credit-card"></i> Payment Information</h5>
                        <div class="alert alert-success">
                            <strong>Payment Method:</strong> <?= Html::encode($booking->payment_method ?? 'M-Pesa') ?><br>
                            <strong>Status:</strong> <span class="badge bg-success">Completed</span><br>
                            <strong>Transaction ID:</strong> <?= Html::encode($booking->qr_code ? json_decode($booking->qr_code, true)['booking_id'] : 'N/A') ?>
                        </div>
                    </div>
                    
                    <!-- QR Code -->
                    <div class="qr-code-section text-center mb-4">
                        <h5 class="mb-3"><i class="bi bi-qr-code"></i> QR Code for Boarding</h5>
                        <div class="qr-code-container">
                            <?php if (isset($qrImageData) && $qrImageData): ?>
                                <img src="data:image/png;base64,<?= $qrImageData ?>" alt="QR Code" class="img-fluid border rounded" style="max-width: 200px;">
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> QR Code could not be generated.
                                </div>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted mt-2 d-block">Scan this QR code when boarding the bus</small>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button onclick="window.print()" class="btn btn-primary btn-lg">
                            <i class="bi bi-printer"></i> Print Receipt
                        </button>
                        <a href="<?= \yii\helpers\Url::to(['pdf-receipt', 'id' => $booking->id]) ?>" class="btn btn-outline-secondary btn-lg" target="_blank">
                            <i class="bi bi-file-pdf"></i> Download PDF
                        </a>
                        <a href="<?= \yii\helpers\Url::to(['my-bookings']) ?>" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul"></i> View My Bookings
                        </a>
                        <a href="<?= \yii\helpers\Url::to(['bus']) ?>" class="btn btn-outline-success">
                            <i class="bi bi-plus-circle"></i> Book Another Ticket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .no-print {
        display: none !important;
    }
    
    .card {
        border: 2px solid #000 !important;
        box-shadow: none !important;
    }
    
    .receipt-details, .payment-info, .qr-code-section {
        page-break-inside: avoid;
    }
}

.qr-code-container {
    background: white;
    padding: 15px;
    border-radius: 8px;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.receipt-details .list-group-item {
    border: none;
    padding: 0.5rem 0;
}

.payment-info .alert {
    border-radius: 8px;
}
</style> 