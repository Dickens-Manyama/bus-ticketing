<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Parcel;

$this->title = 'Parcel Details - ' . $model->tracking_number;
$this->params['breadcrumbs'][] = ['label' => 'Parcel Delivery Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'My Parcels', 'url' => ['my-parcels']];
$this->params['breadcrumbs'][] = $model->tracking_number;
?>

<div class="parcel-view container py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>Parcel Details
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="bi bi-upc-scan text-primary"></i> Tracking Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Tracking Number:</strong></td>
                                    <td><span class="badge bg-dark fs-6"><?= Html::encode($model->tracking_number) ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><?= $model->getStatusBadge() ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Status:</strong></td>
                                    <td><?= $model->getPaymentStatusBadge() ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td><?= date('Y-m-d H:i:s', $model->created_at) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Route:</strong></td>
                                    <td><?= Html::encode($model->route->name ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Price:</strong></td>
                                    <td><span class="text-success fw-bold"><?= number_format($model->price, 0, '.', ',') ?> TZS</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-box-seam text-success"></i> Parcel Information</h5>
                            <table class="table table-borderless">
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
                                    <td><strong>Dimensions:</strong></td>
                                    <td><?= Html::encode($model->dimensions) ?> cm</td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td><?= Html::encode($model->description ?: 'No description') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Departure Date:</strong></td>
                                    <td><?= $model->departure_date ? date('Y-m-d', $model->departure_date) : 'Not set' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-person me-2"></i>Sender Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> <?= Html::encode($model->sender_name) ?></p>
                            <p><strong>Phone:</strong> <?= Html::encode($model->sender_phone) ?></p>
                            <p><strong>Address:</strong> <?= Html::encode($model->sender_address ?: 'Not provided') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Recipient Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> <?= Html::encode($model->recipient_name) ?></p>
                            <p><strong>Phone:</strong> <?= Html::encode($model->recipient_phone) ?></p>
                            <p><strong>Address:</strong> <?= Html::encode($model->recipient_address ?: 'Not provided') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if ($model->payment_status !== Parcel::PAYMENT_STATUS_PAID): ?>
                        <div class="col-md-4 mb-2">
                            <?= Html::a('<i class="bi bi-credit-card me-2"></i>Make Payment', ['payment', 'id' => $model->id], ['class' => 'btn btn-success w-100']) ?>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-4 mb-2">
                            <?= Html::a('<i class="bi bi-download me-2"></i>Download Receipt', ['receipt', 'id' => $model->id], ['class' => 'btn btn-primary w-100', 'target' => '_blank']) ?>
                        </div>
                        <div class="col-md-4 mb-2">
                            <?= Html::a('<i class="bi bi-arrow-left me-2"></i>Back to My Parcels', ['my-parcels'], ['class' => 'btn btn-secondary w-100']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h5><i class="bi bi-info-circle text-warning"></i> Important Information</h5>
                <div class="alert alert-info">
                    <ul class="mb-0">
                        <li>Please keep your tracking number safe for future reference</li>
                        <li>Contact us if you have any questions about your parcel</li>
                        <li>Delivery times may vary depending on the route and conditions</li>
                        <li><strong>Recipient must present valid ID for parcel collection</strong></li>
                        <li><strong>Staff will scan this QR code to verify and release your parcel</strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-qr-code me-2"></i>QR Code</h5>
                </div>
                <div class="card-body text-center">
                    <div id="qrcode" class="mb-3"></div>
                    <p class="text-muted small">Scan this QR code to track your parcel</p>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary btn-sm" onclick="printQR()">
                            <i class="bi bi-printer me-2"></i>Print QR Code
                        </button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Tracking Status</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item <?= in_array($model->status, ['pending', 'confirmed', 'in_transit', 'delivered']) ? 'active' : '' ?>">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Parcel Booked</h6>
                                <p class="text-muted">Parcel booking confirmed</p>
                            </div>
                        </div>
                        <div class="timeline-item <?= in_array($model->status, ['confirmed', 'in_transit', 'delivered']) ? 'active' : '' ?>">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Payment Confirmed</h6>
                                <p class="text-muted">Payment received and confirmed</p>
                            </div>
                        </div>
                        <div class="timeline-item <?= in_array($model->status, ['in_transit', 'delivered']) ? 'active' : '' ?>">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>In Transit</h6>
                                <p class="text-muted">Parcel is on its way</p>
                            </div>
                        </div>
                        <div class="timeline-item <?= $model->status === 'delivered' ? 'active' : '' ?>">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Delivered</h6>
                                <p class="text-muted">Parcel delivered to recipient</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate QR code
    var qrData = <?= json_encode($model->qr_code) ?>;
    QRCode.toCanvas(document.getElementById('qrcode'), qrData, {
        width: 200,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        }
    }, function (error) {
        if (error) console.error(error);
    });
});

function printQR() {
    var printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Parcel QR Code - <?= $model->tracking_number ?></title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
                    .qr-container { margin: 20px 0; }
                    .tracking-number { font-size: 18px; font-weight: bold; margin: 10px 0; }
                </style>
            </head>
            <body>
                <h2>Parcel QR Code</h2>
                <div class="tracking-number">Tracking: <?= $model->tracking_number ?></div>
                <div class="qr-container" id="qrcode-print"></div>
                <p>Scan this QR code to track your parcel</p>
            </body>
        </html>
    `);
    
    QRCode.toCanvas(printWindow.document.getElementById('qrcode-print'), <?= json_encode($model->qr_code) ?>, {
        width: 300,
        margin: 2
    }, function (error) {
        if (!error) {
            printWindow.print();
        }
    });
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #ddd;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #ddd;
}

.timeline-item.active .timeline-marker {
    background: #28a745;
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: -29px;
    top: 17px;
    width: 2px;
    height: 30px;
    background: #ddd;
}

.timeline-item.active::after {
    background: #28a745;
}

.timeline-content h6 {
    margin: 0;
    font-weight: bold;
}

.timeline-content p {
    margin: 5px 0 0 0;
    font-size: 0.9em;
}
</style> 