<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Parcel */
/* @var $qrImageData string|null */
/* @var $receiptUrl string */

$this->title = 'Parcel Receipt';
$this->params['breadcrumbs'][] = ['label' => 'Parcels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="parcel-receipt">
    <div class="text-end mb-3">
        <?= Html::button('Print Receipt', [
            'class' => 'btn btn-primary',
            'onclick' => 'window.print()'
        ]) ?>
    </div>
    
    <div class="card" id="receipt-content">
        <div class="card-body">
            <div class="text-center mb-4">
                <h2>PARCEL RECEIPT</h2>
                <p class="text-muted">Bus Ticketing System</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>Parcel Information</h5>
                    <table class="table table-borderless">
                        <tr><td><strong>Tracking Number:</strong></td><td><?= Html::encode($model->tracking_number) ?></td></tr>
                        <tr><td><strong>Type:</strong></td><td><?= Html::encode($model->getParcelTypeLabels()[$model->parcel_type] ?? $model->parcel_type) ?></td></tr>
                        <tr><td><strong>Category:</strong></td><td><?= Html::encode($model->getParcelCategoryLabels()[$model->parcel_category] ?? $model->parcel_category) ?></td></tr>
                        <tr><td><strong>Weight:</strong></td><td><?= Html::encode($model->weight) ?> kg</td></tr>
                        <tr><td><strong>Route:</strong></td><td><?= Html::encode($model->route_id) ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Contact Information</h5>
                    <table class="table table-borderless">
                        <tr><td><strong>Sender:</strong></td><td><?= Html::encode($model->sender_name) ?></td></tr>
                        <tr><td><strong>Sender Phone:</strong></td><td><?= Html::encode($model->sender_phone) ?></td></tr>
                        <tr><td><strong>Recipient:</strong></td><td><?= Html::encode($model->recipient_name) ?></td></tr>
                        <tr><td><strong>Recipient Phone:</strong></td><td><?= Html::encode($model->recipient_phone) ?></td></tr>
                    </table>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <h5>Payment Information</h5>
                    <table class="table table-borderless">
                        <tr><td><strong>Amount:</strong></td><td><strong><?= number_format($model->price, 0) ?> TZS</strong></td></tr>
                        <tr><td><strong>Payment Status:</strong></td><td><?= Html::encode($model->getPaymentStatusLabels()[$model->payment_status] ?? $model->payment_status) ?></td></tr>
                        <tr><td><strong>Payment Date:</strong></td><td><?= $model->payment_date ? date('Y-m-d H:i:s', $model->payment_date) : 'N/A' ?></td></tr>
                        <tr><td><strong>Booking Date:</strong></td><td><?= date('Y-m-d H:i:s', $model->created_at) ?></td></tr>
                    </table>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted">Thank you for using our service!</p>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <?= Html::a('Back to Parcels', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>

<style>
@media print {
    .btn, .breadcrumb, .mt-3 { display: none !important; }
    #receipt-content { border: none !important; }
    .card { box-shadow: none !important; }
}
</style> 