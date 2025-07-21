<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ParcelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Parcels Management';
$this->params['breadcrumbs'][] = $this->title;

// Check user role
$isSuperAdmin = Yii::$app->user->identity && Yii::$app->user->identity->role === 'super_admin';
?>
<div class="parcel-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Parcel', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Export to Excel', ['export'], ['class' => 'btn btn-info']) ?>
    </p>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'tracking_number',
            [
                'attribute' => 'user_id',
                'value' => function($model) { return $model->user ? $model->user->username : 'N/A'; },
                'label' => 'User',
            ],
            'parcel_type',
            'parcel_category',
            'weight',
            [
                'attribute' => 'route_id',
                'value' => function($model) { return $model->route ? $model->route->name : 'N/A'; },
                'label' => 'Route',
            ],
            'price',
            [
                'attribute' => 'status',
                'value' => function($model) { return $model->getStatusBadge(); },
                'format' => 'raw',
            ],
            [
                'attribute' => 'payment_status',
                'value' => function($model) { return $model->getPaymentStatusBadge(); },
                'format' => 'raw',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}' . ($isSuperAdmin ? ' {delete}' : '') . ' {process-payment}',
                'buttons' => [
                    'process-payment' => function($url, $model, $key) {
                        if ($model->payment_status !== $model::PAYMENT_STATUS_PAID) {
                            return Html::a('<i class="bi bi-cash-coin"></i>', ['process-payment', 'id' => $model->id], [
                                'title' => 'Process Payment',
                                'data-method' => 'post',
                                'data-confirm' => 'Mark this parcel as paid?'
                            ]);
                        }
                        return '';
                    }
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div> 