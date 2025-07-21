<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Parcel */
/* @var $qrImageData string|null */
/* @var $receiptUrl string */

$this->title = 'Parcel: ' . $model->tracking_number;
$this->params['breadcrumbs'][] = ['label' => 'Parcels Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Check user role
$isSuperAdmin = Yii::$app->user->identity && Yii::$app->user->identity->role === 'superadmin';
?>
<div class="parcel-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if ($isSuperAdmin): ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this parcel?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
        <?php if ($model->payment_status !== $model::PAYMENT_STATUS_PAID): ?>
            <?= Html::a('Make Payment', ['payment', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php else: ?>
            <?= Html::a('View Receipt', ['receipt', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?php endif; ?>
        <?= Html::a('Update Status', ['update-status', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tracking_number',
            [
                'attribute' => 'user_id',
                'value' => $model->user ? $model->user->username : 'N/A',
                'label' => 'User',
            ],
            'parcel_type',
            'parcel_category',
            'weight',
            [
                'attribute' => 'route_id',
                'value' => $model->route ? $model->route->name : 'N/A',
                'label' => 'Route',
            ],
            'price',
            [
                'attribute' => 'status',
                'value' => $model->getStatusBadge(),
                'format' => 'raw',
            ],
            [
                'attribute' => 'payment_status',
                'value' => $model->getPaymentStatusBadge(),
                'format' => 'raw',
            ],
            'payment_method',
            'payment_date:datetime',
            'sender_name',
            'sender_phone',
            'sender_address',
            'recipient_name',
            'recipient_phone',
            'recipient_address',
            'departure_date:date',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
    <div class="mt-4">
        <h5>QR Code for Verification</h5>
        <?php if ($qrImageData): ?>
            <img src="data:image/png;base64,<?= $qrImageData ?>" alt="QR Code" style="max-width:200px;">
            <div><small><?= Html::a('Mobile Verify Link', $receiptUrl, ['target' => '_blank']) ?></small></div>
        <?php else: ?>
            <div class="alert alert-warning">QR code could not be generated.</div>
        <?php endif; ?>
    </div>
</div> 