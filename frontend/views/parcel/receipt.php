<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Parcel;

$this->title = 'Payment Successful - Parcel Receipt';
$this->params['breadcrumbs'][] = ['label' => 'Parcel Delivery Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'My Parcels', 'url' => ['my-parcels']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="parcel-receipt container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="alert alert-success text-center mb-4">
                <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                <h3 class="text-success">Payment Successful!</h3>
                <p class="mb-0">Your parcel has been booked successfully via <?= Html::encode($model->payment_method) ?></p>
            </div>

            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>Parcel Receipt
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Company Header -->
                    <div class="text-center mb-4">
                        <h2 class="text-primary mb-1">
                            <i class="bi bi-bus-front"></i> Dickens Bus Company
                        </h2>
                        <p class="text-muted mb-0">Parcel Delivery Service</p>
                        <p class="text-muted small">dickensmanyama8@gmail.com | +255679165468</p>
                    </div>

                    <hr>

                    <!-- Receipt Details -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary"><i class="bi bi-upc-scan me-2"></i>Tracking Information</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Receipt #:</strong></td>
                                            <td><?= Html::encode($model->tracking_number) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td><?= date('Y-m-d H:i:s', $model->created_at) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td><?= $model->getStatusBadge() ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment:</strong></td>
                                            <td><?= $model->getPaymentStatusBadge() ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success"><i class="bi bi-box-seam me-2"></i>Parcel Details</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td><?= Html::encode(ucfirst($model->parcel_type)) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Category:</strong></td>
                                            <td><?= Html::encode($model->getParcelCategoryLabels()[$model->parcel_category] ?? 'Unknown') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Weight:</strong></td>
                                            <td><?= Html::encode($model->weight) ?> kg</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Route:</strong></td>
                                            <td><?= Html::encode($model->route->name ?? 'N/A') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="qr-code-container">
                                <?php if (isset($qrImageData) && $qrImageData): ?>
                                    <img src="data:image/png;base64,<?= $qrImageData ?>" alt="QR Code" class="img-fluid border rounded" style="max-width: 120px;">
                                    <div class="mt-2">
                                        <small class="text-muted">Scan to verify</small>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning small">
                                        <i class="bi bi-exclamation-triangle"></i> QR Code unavailable
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Sender & Recipient -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-info"><i class="bi bi-person me-2"></i>Sender Information</h6>
                            <div class="border rounded p-3">
                                <p class="mb-1"><strong><?= Html::encode($model->sender_name) ?></strong></p>
                                <p class="mb-1 text-muted"><?= Html::encode($model->sender_phone) ?></p>
                                <p class="mb-0 text-muted small"><?= Html::encode($model->sender_address ?: 'Address not provided') ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="bi bi-person-check me-2"></i>Recipient Information</h6>
                            <div class="border rounded p-3">
                                <p class="mb-1"><strong><?= Html::encode($model->recipient_name) ?></strong></p>
                                <p class="mb-1 text-muted"><?= Html::encode($model->recipient_phone) ?></p>
                                <p class="mb-0 text-muted small"><?= Html::encode($model->recipient_address ?: 'Address not provided') ?></p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Payment Summary -->
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-warning"><i class="bi bi-cash-coin me-2"></i>Payment Summary</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Parcel Fee:</td>
                                    <td class="text-end"><?= number_format($model->price, 0, '.', ',') ?> TZS</td>
                                </tr>
                                <tr>
                                    <td>Service Fee:</td>
                                    <td class="text-end">0 TZS</td>
                                </tr>
                                <tr class="table-active fw-bold">
                                    <td>Total Amount:</td>
                                    <td class="text-end text-success"><?= number_format($model->price, 0, '.', ',') ?> TZS</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-primary"><i class="bi bi-info-circle me-2"></i>Important Notes</h6>
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-check text-success"></i> Keep this receipt safe</li>
                                <li><i class="bi bi-check text-success"></i> Present ID for collection</li>
                                <li><i class="bi bi-check text-success"></i> Contact us for support</li>
                                <li><i class="bi bi-check text-success"></i> Track online anytime</li>
                            </ul>
                        </div>
                    </div>

                    <hr>

                    <!-- QR Code Section -->
                    <div class="qr-code-section text-center mb-4">
                        <h5 class="mb-3"><i class="bi bi-qr-code"></i> QR Code for Verification</h5>
                        <div class="qr-code-container">
                            <?php if (isset($qrImageData) && $qrImageData): ?>
                                <img src="data:image/png;base64,<?= $qrImageData ?>" alt="QR Code" class="img-fluid border rounded" style="max-width: 200px;">
                                <div class="mt-2">
                                    <small class="text-muted">Scan this QR code to verify and release your parcel</small>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> QR Code could not be generated.
                                    <br>
                                    <small>Staff can manually verify using tracking number: <strong><?= Html::encode($model->tracking_number) ?></strong></small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center">
                        <p class="text-muted small mb-1">Thank you for choosing Dickens Bus Company</p>
                        <p class="text-muted small mb-0">For support: +255679165468 | dickensmanyama8@gmail.com</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <button class="btn btn-primary btn-lg" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>Print Receipt
                </button>
                <?= Html::a('<i class="bi bi-download me-2"></i>Download PDF', ['receipt', 'id' => $model->id, 'format' => 'pdf'], [
                    'class' => 'btn btn-success btn-lg ms-2',
                    'target' => '_blank'
                ]) ?>
                <?= Html::a('<i class="bi bi-arrow-left me-2"></i>Back to My Parcels', ['my-parcels'], [
                    'class' => 'btn btn-secondary btn-lg ms-2'
                ]) ?>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .alert {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .card-header {
        background: #000 !important;
        color: #fff !important;
    }
}

.qr-code-container {
    display: inline-block;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.payment-success {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style> 