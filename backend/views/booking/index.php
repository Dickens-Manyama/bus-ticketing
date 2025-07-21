<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BookingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bookings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Export CSV', array_merge(['export-csv'], Yii::$app->request->queryParams), ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('Export Excel', array_merge(['export-excel'], Yii::$app->request->queryParams), ['class' => 'btn btn-outline-success']) ?>
        <?= Html::a('Print Report', ['#'], ['class' => 'btn btn-outline-primary', 'onclick' => 'window.print(); return false;']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'user_id',
                'label' => 'User',
                'value' => function($model) { return $model->user->username; }
            ],
            [
                'attribute' => 'bus_id',
                'label' => 'Bus',
                'value' => function($model) { return $model->bus->type . ' (' . $model->bus->plate_number . ')'; }
            ],
            [
                'attribute' => 'route_id',
                'label' => 'Route',
                'value' => function($model) { return $model->route->origin . ' → ' . $model->route->destination; }
            ],
            [
                'attribute' => 'seat_id',
                'label' => 'Seat',
                'value' => function($model) { return $model->seat->seat_number; }
            ],
            // [
            //     'attribute' => 'status',
            //     'filter' => \common\models\Booking::getStatusOptions(),
            //     'contentOptions' => ['class' => 'text-secondary']
            // ],
            // [
            //     'attribute' => 'payment_method',
            //     'filter' => \common\models\Booking::getPaymentMethodOptions(),
            // ],
            // [
            //     'attribute' => 'payment_status',
            //     'filter' => \common\models\Booking::getPaymentStatusOptions(),
            //     'contentOptions' => ['class' => 'text-secondary']
            // ],
            [
                'attribute' => 'ticket_status',
                'label' => 'Ticket Status',
                'filter' => \common\models\Booking::getTicketStatusOptions(),
                'value' => function($model) {
                    $statusLabels = [
                        'active' => '🟢 Active',
                        'used' => '🔴 Used',
                        'expired' => '🟡 Expired'
                    ];
                    return $statusLabels[$model->ticket_status] ?? $model->ticket_status;
                },
                'contentOptions' => ['class' => 'text-secondary']
            ],
            [
                'attribute' => 'scanned_at',
                'label' => 'Scanned At',
                'value' => function($model) {
                    if ($model->scanned_at) {
                        return '<span class="badge bg-primary">🔵 Scanned</span><br><small>' . date('Y-m-d H:i', $model->scanned_at) . '</small>';
                    }
                    return $model->ticket_status === 'active' ? '<span class="badge bg-danger">🔴 Not Scanned</span>' : '<span class="badge bg-secondary">-</span>';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center']
            ],
            [
                'attribute' => 'scanned_by',
                'label' => 'Scanned By',
                'value' => function($model) {
                    if ($model->scannedBy) {
                        return '<span class="badge bg-primary">🔵 Scanned</span><br><small>' . $model->scannedBy->username . ' (' . $model->scannedBy->role . ')</small>';
                    } elseif ($model->ticket_status === 'used' && $model->scanned_at) {
                        return '<span class="badge bg-info">📱 Scanned by Phone</span><br><small>Mobile QR Verification</small>';
                    }
                    return $model->ticket_status === 'active' ? '<span class="badge bg-danger">🔴 Not Scanned</span>' : '<span class="badge bg-secondary">-</span>';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center']
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
            ],
        ],
    ]) ?>
</div> 