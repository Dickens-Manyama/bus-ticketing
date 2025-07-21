<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Booking */

$this->title = 'Booking: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bookings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this booking?',
                'method' => 'post',
            ],
        ]) ?>
        
        <?php if ($model->isActive()): ?>
            <?= Html::a('✅ Verify Ticket', ['verify-ticket', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data' => [
                    'confirm' => 'Are you sure you want to verify this ticket and allow boarding?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php elseif ($model->isUsed()): ?>
            <span class="btn btn-secondary" disabled>🔴 Ticket Already Used</span>
            <?php if (Yii::$app->user->identity->isAdmin() || Yii::$app->user->identity->isSuperAdmin()): ?>
                <?= Html::a('🔄 Reset Ticket', ['reset-ticket', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Are you sure you want to reset this ticket to active status? This will allow it to be used again.',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        <?php elseif ($model->isExpired()): ?>
            <span class="btn btn-secondary" disabled>🟡 Ticket Expired</span>
            <?php if (Yii::$app->user->identity->isAdmin() || Yii::$app->user->identity->isSuperAdmin()): ?>
                <?= Html::a('🔄 Reset Ticket', ['reset-ticket', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Are you sure you want to reset this ticket to active status?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        <?php endif; ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'User',
                'value' => $model->user->username,
            ],
            [
                'label' => 'Bus',
                'value' => $model->bus->type . ' (' . $model->bus->plate_number . ')',
            ],
            [
                'label' => 'Route',
                'value' => $model->route->origin . ' → ' . $model->route->destination,
            ],
            [
                'label' => 'Seat',
                'value' => $model->seat->seat_number,
            ],
            'payment_info',
            [
                'attribute' => 'ticket_status',
                'label' => 'Ticket Status',
                'value' => function($model) {
                    $statusLabels = [
                        'active' => '🟢 Active (Ready for boarding)',
                        'used' => '🔴 Used (Already boarded)',
                        'expired' => '🟡 Expired (Cannot be used)'
                    ];
                    return $statusLabels[$model->ticket_status] ?? $model->ticket_status;
                },
                'contentOptions' => [
                    'class' => 'text-success'
                ]
            ],
            [
                'attribute' => 'scanned_at',
                'label' => 'Scanned At',
                'value' => function($model) {
                    if ($model->scanned_at) {
                        return '<span class="badge bg-primary">🔵 Scanned</span><br><small>' . date('F j, Y \a\t g:i A', $model->scanned_at) . '</small>';
                    }
                    return $model->ticket_status === 'active' ? '<span class="badge bg-danger">🔴 Not Scanned</span>' : '<span class="badge bg-secondary">N/A</span>';
                },
                'format' => 'raw',
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
                    return $model->ticket_status === 'active' ? '<span class="badge bg-danger">🔴 Not Scanned</span>' : '<span class="badge bg-secondary">N/A</span>';
                },
                'format' => 'raw',
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div> 