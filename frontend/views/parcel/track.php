<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Parcel;

$this->title = 'Track Parcel';
$this->params['breadcrumbs'][] = ['label' => 'Parcel Delivery Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="parcel-track container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-search me-2"></i>Track Your Parcel
                    </h4>
                </div>
                <div class="card-body">
                    <form method="get" class="mb-4">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" name="tracking_number" 
                                   placeholder="Enter tracking number (e.g., PKL2024ABC123)" 
                                   value="<?= Html::encode($tracking_number) ?>" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Track
                            </button>
                        </div>
                    </form>

                    <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= Html::encode($error) ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($model): ?>
                    <div class="tracking-result">
                        <div class="row">
                            <div class="col-md-8">
                                <h5><i class="bi bi-box-seam text-primary"></i> Parcel Information</h5>
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
                                        <td><strong>Type:</strong></td>
                                        <td><?= Html::encode(ucfirst($model->parcel_type)) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Route:</strong></td>
                                        <td><?= Html::encode($model->route->name ?? 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Weight:</strong></td>
                                        <td><?= Html::encode($model->weight) ?> kg</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td><?= date('Y-m-d H:i:s', $model->created_at) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <h5><i class="bi bi-people text-success"></i> Contact Information</h5>
                                <div class="mb-3">
                                    <strong>Sender:</strong><br>
                                    <?= Html::encode($model->sender_name) ?><br>
                                    <small class="text-muted"><?= Html::encode($model->sender_phone) ?></small>
                                </div>
                                <div>
                                    <strong>Recipient:</strong><br>
                                    <?= Html::encode($model->recipient_name) ?><br>
                                    <small class="text-muted"><?= Html::encode($model->recipient_phone) ?></small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5><i class="bi bi-map text-info"></i> Tracking Timeline</h5>
                        <div class="timeline">
                            <div class="timeline-item <?= in_array($model->status, ['pending', 'confirmed', 'in_transit', 'delivered']) ? 'active' : '' ?>">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>Parcel Booked</h6>
                                    <p class="text-muted"><?= date('Y-m-d H:i', $model->created_at) ?></p>
                                    <p>Parcel booking has been confirmed and is ready for processing.</p>
                                </div>
                            </div>
                            
                            <?php if ($model->payment_status === Parcel::PAYMENT_STATUS_PAID): ?>
                            <div class="timeline-item active">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>Payment Confirmed</h6>
                                    <p class="text-muted">Payment received and confirmed</p>
                                    <p>Payment has been processed successfully. Parcel is ready for pickup.</p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (in_array($model->status, ['confirmed', 'in_transit', 'delivered'])): ?>
                            <div class="timeline-item active">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>Parcel Confirmed</h6>
                                    <p class="text-muted">Parcel has been confirmed and is ready for transport</p>
                                    <p>Your parcel has been verified and is scheduled for delivery.</p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (in_array($model->status, ['in_transit', 'delivered'])): ?>
                            <div class="timeline-item active">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>In Transit</h6>
                                    <p class="text-muted">Parcel is on its way to destination</p>
                                    <p>Your parcel is currently being transported to the destination.</p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($model->status === 'delivered'): ?>
                            <div class="timeline-item active">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>Delivered</h6>
                                    <p class="text-muted">Parcel has been delivered to recipient</p>
                                    <p>Your parcel has been successfully delivered to the recipient.</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <h5><i class="bi bi-info-circle text-warning"></i> Important Information</h5>
                            <div class="alert alert-info">
                                <ul class="mb-0">
                                    <li>Please keep your tracking number safe for future reference</li>
                                    <li>Contact us if you have any questions about your parcel</li>
                                    <li>Delivery times may vary depending on the route and conditions</li>
                                    <li>Recipient must present valid ID for parcel collection</li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <?= Html::a('<i class="bi bi-arrow-left me-2"></i>Track Another Parcel', ['track'], ['class' => 'btn btn-secondary']) ?>
                            <?= Html::a('<i class="bi bi-house me-2"></i>Back to Home', ['index'], ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
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
    height: 40px;
    background: #ddd;
}

.timeline-item.active::after {
    background: #28a745;
}

.timeline-content h6 {
    margin: 0;
    font-weight: bold;
    color: #333;
}

.timeline-content p {
    margin: 5px 0 0 0;
    font-size: 0.9em;
}

.tracking-result {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
}
</style> 