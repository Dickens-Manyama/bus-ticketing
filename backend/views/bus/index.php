<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
?>
<div class="bus-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Bus', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Export CSV', array_merge(['export-csv'], Yii::$app->request->queryParams), ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('Export Excel', array_merge(['export-excel'], Yii::$app->request->queryParams), ['class' => 'btn btn-outline-success']) ?>
        <?= Html::a('Print Report', ['#'], ['class' => 'btn btn-outline-primary', 'onclick' => 'window.print(); return false;']) ?>
    </p>
    <?php Pjax::begin(['id' => 'bus-pjax']); ?>
    <form id="bulk-delete-form" method="post" action="<?= Url::to(['bus/bulk-delete']) ?>">
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
            [
                'attribute' => 'type',
                'filter' => ['Luxury' => 'Luxury', 'Semi-Luxury' => 'Semi-Luxury', 'Middle Class' => 'Middle Class'],
            ],
            'plate_number',
            'seat_count',
            [
                'attribute' => 'image',
                'format' => 'html',
                'value' => function($model) {
                    return $model->image ? Html::img($model->image, ['style' => 'max-width:80px;']) : null;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
            ],
        ],
    ]) ?>
    </form>
    <?php Pjax::end(); ?>
</div> 
<script>
// Select all checkboxes
$(document).on('change', '.select-on-check-all', function() {
    var checked = $(this).prop('checked');
    $('input[name="selection[]"]').prop('checked', checked);
});
</script> 