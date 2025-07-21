<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Parcel */

$this->title = 'Review Parcel Information';
$this->params['breadcrumbs'][] = ['label' => 'Parcels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parcel-review">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="card mb-4">
        <div class="card-body">
            <h4>Parcel Details</h4>
            <table class="table table-bordered">
                <tr><th>Tracking Number</th><td><?= Html::encode($model->tracking_number) ?></td></tr>
                <tr><th>Type</th><td><?= Html::encode($model->getParcelTypeLabels()[$model->parcel_type] ?? $model->parcel_type) ?></td></tr>
                <tr><th>Category</th><td><?= Html::encode($model->getParcelCategoryLabels()[$model->parcel_category] ?? $model->parcel_category) ?></td></tr>
                <tr><th>Weight (kg)</th><td><?= Html::encode($model->weight) ?></td></tr>
                <tr><th>Route</th><td><?= Html::encode($model->route_id) ?></td></tr>
                <tr><th>Sender</th><td><?= Html::encode($model->sender_name) ?> (<?= Html::encode($model->sender_phone) ?>)</td></tr>
                <tr><th>Recipient</th><td><?= Html::encode($model->recipient_name) ?> (<?= Html::encode($model->recipient_phone) ?>)</td></tr>
                <tr><th>Price</th><td><strong><?= number_format($model->price, 0) ?> TZS</strong></td></tr>
                <tr><th>Status</th><td><?= Html::encode($model->getStatusLabels()[$model->status] ?? $model->status) ?></td></tr>
            </table>
        </div>
    </div>
    <div class="form-group">
        <?= Html::a('Continue with Payment', ['payment', 'id' => $model->id], ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div> 