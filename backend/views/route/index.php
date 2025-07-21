<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
?>
<div class="route-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php if (!Yii::$app->user->identity->isStaff()): ?>
            <?= Html::a('Create Route', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        <?= Html::a('Export CSV', array_merge(['export-csv'], Yii::$app->request->queryParams), ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('Export Excel', array_merge(['export-excel'], Yii::$app->request->queryParams), ['class' => 'btn btn-outline-success']) ?>
        <?= Html::a('Print Report', ['#'], ['class' => 'btn btn-outline-primary', 'onclick' => 'window.print(); return false;']) ?>
    </p>
    <?php if (Yii::$app->user->identity && method_exists(Yii::$app->user->identity, 'isSuperAdmin') && Yii::$app->user->identity->isSuperAdmin()): ?>
    <form id="bulk-delete-form" method="post" action="<?= Url::to(['route/bulk-delete']) ?>">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <div class="mb-2">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete the selected routes?')">
                <i class="bi bi-trash"></i> Delete Selected
            </button>
        </div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function($model) {
                        return ['value' => $model->id];
                    },
                    'headerOptions' => ['style' => 'width:30px'],
                ],
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                'origin',
                'destination',
                'price',
                'distance',
                [
                    'attribute' => 'departure_time',
                    'format' => 'time',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            if (Yii::$app->user->identity->isStaff()) {
                                return '';
                            }
                            return Html::a('<i class="bi bi-pencil"></i>', $url, [
                                'class' => 'btn btn-sm btn-outline-warning',
                                'title' => 'Update',
                            ]);
                        },
                        'delete' => function ($url, $model) {
                            if (Yii::$app->user->identity->isStaff()) {
                                return '';
                            }
                            return Html::a('<i class="bi bi-trash"></i>', $url, [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'title' => 'Delete',
                                'data-confirm' => 'Are you sure you want to delete this route?',
                                'data-method' => 'post',
                            ]);
                        },
                    ],
                ],
            ],
        ]) ?>
    </form>
    <script>
    // Select all checkboxes
    $(document).on('change', '.select-on-check-all', function() {
        var checked = $(this).prop('checked');
        $('input[name="selection[]"]').prop('checked', checked);
    });
    </script>
    <?php else: ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'origin',
            'destination',
            'price',
            'distance',
            [
                'attribute' => 'departure_time',
                'format' => 'time',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        if (Yii::$app->user->identity->isStaff()) {
                            return '';
                        }
                        return Html::a('<i class="bi bi-pencil"></i>', $url, [
                            'class' => 'btn btn-sm btn-outline-warning',
                            'title' => 'Update',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if (Yii::$app->user->identity->isStaff()) {
                            return '';
                        }
                        return Html::a('<i class="bi bi-trash"></i>', $url, [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'title' => 'Delete',
                            'data-confirm' => 'Are you sure you want to delete this route?',
                            'data-method' => 'post',
                        ]);
                    },
                ],
            ],
        ],
    ]) ?>
    <?php endif; ?>
</div> 