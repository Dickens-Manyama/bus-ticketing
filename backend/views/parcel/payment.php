<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Parcel */

$this->title = 'Parcel Payment';
$this->params['breadcrumbs'][] = ['label' => 'Parcels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parcel-payment">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="card mb-4">
        <div class="card-body">
            <h4>Parcel Details</h4>
            <table class="table table-bordered">
                <tr><th>Tracking Number</th><td><?= Html::encode($model->tracking_number) ?></td></tr>
                <tr><th>Type</th><td><?= Html::encode($model->getParcelTypeLabels()[$model->parcel_type] ?? $model->parcel_type) ?></td></tr>
                <tr><th>Weight (kg)</th><td><?= Html::encode($model->weight) ?></td></tr>
                <tr><th>Price</th><td><strong><?= number_format($model->price, 0) ?> TZS</strong></td></tr>
                <tr><th>Status</th><td><?= Html::encode($model->getStatusLabels()[$model->status] ?? $model->status) ?></td></tr>
                <tr><th>Payment Status</th><td><?= Html::encode($model->getPaymentStatusLabels()[$model->payment_status] ?? $model->payment_status) ?></td></tr>
            </table>
        </div>
    </div>
    <?php if ($model->payment_status !== $model::PAYMENT_STATUS_PAID): ?>
        <?= Html::beginForm(['payment', 'id' => $model->id], 'post') ?>
            <div class="form-group">
                <?= Html::submitButton('Mark as Paid', ['class' => 'btn btn-success btn-lg']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>
        <?= Html::endForm() ?>
    <?php else: ?>
        <div class="alert alert-success">This parcel has already been paid.</div>
        <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-primary']) ?>
    <?php endif; ?>
</div> 