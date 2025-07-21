<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Parcel;

$this->title = 'My Parcels';
$this->params['breadcrumbs'][] = ['label' => 'Parcel Delivery Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="parcel-my-parcels container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>My Parcels
                    </h4>
                    <?= Html::a('<i class="bi bi-plus-circle me-2"></i>Book New Parcel', ['create'], ['class' => 'btn btn-light']) ?>
                </div>
                <div class="card-body">
                    <?php if (empty($parcels)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam display-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No parcels found</h5>
                        <p class="text-muted">You haven't booked any parcels yet.</p>
                        <?= Html::a('<i class="bi bi-plus-circle me-2"></i>Book Your First Parcel', ['create'], ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php else: ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Parcel History (<?= count($parcels) ?> parcels)</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <?= Html::a('<i class="bi bi-search me-2"></i>Track Parcel', ['track'], ['class' => 'btn btn-outline-primary']) ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Type</th>
                                    <th>Route</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($parcels as $parcel): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-dark"><?= Html::encode($parcel->tracking_number) ?></span>
                                    </td>
                                    <td>
                                        <i class="bi bi-box-seam text-primary me-1"></i>
                                        <?= Html::encode(ucfirst($parcel->parcel_type)) ?>
                                        <br>
                                        <small class="text-muted"><?= Html::encode($parcel->getParcelCategoryLabels()[$parcel->parcel_category] ?? 'Unknown') ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="bi bi-arrow-right text-muted"></i>
                                            <?= Html::encode($parcel->route->origin ?? 'N/A') ?> →
                                            <?= Html::encode($parcel->route->destination ?? 'N/A') ?>
                                        </small>
                                    </td>
                                    <td><?= $parcel->getStatusBadge() ?></td>
                                    <td><?= $parcel->getPaymentStatusBadge() ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('M d, Y', $parcel->created_at) ?><br>
                                            <?= date('H:i', $parcel->created_at) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $parcel->id], [
                                                'class' => 'btn btn-outline-primary',
                                                'title' => 'View Details'
                                            ]) ?>
                                            
                                            <?php if ($parcel->payment_status !== Parcel::PAYMENT_STATUS_PAID): ?>
                                            <?= Html::a('<i class="bi bi-credit-card"></i>', ['payment', 'id' => $parcel->id], [
                                                'class' => 'btn btn-outline-success',
                                                'title' => 'Make Payment'
                                            ]) ?>
                                            <?php endif; ?>
                                            
                                            <?= Html::a('<i class="bi bi-download"></i>', ['receipt', 'id' => $parcel->id], [
                                                'class' => 'btn btn-outline-info',
                                                'title' => 'Download Receipt',
                                                'target' => '_blank'
                                            ]) ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mt-4">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock display-4"></i>
                                    <h5><?= count(array_filter($parcels, fn($p) => $p->status === Parcel::STATUS_PENDING)) ?></h5>
                                    <p class="mb-0">Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-truck display-4"></i>
                                    <h5><?= count(array_filter($parcels, fn($p) => $p->status === Parcel::STATUS_IN_TRANSIT)) ?></h5>
                                    <p class="mb-0">In Transit</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle display-4"></i>
                                    <h5><?= count(array_filter($parcels, fn($p) => $p->status === Parcel::STATUS_DELIVERED)) ?></h5>
                                    <p class="mb-0">Delivered</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <i class="bi bi-currency-dollar display-4"></i>
                                    <h5><?= count(array_filter($parcels, fn($p) => $p->payment_status === Parcel::PAYMENT_STATUS_PAID)) ?></h5>
                                    <p class="mb-0">Paid</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    font-weight: 600;
    font-size: 0.9rem;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style> 