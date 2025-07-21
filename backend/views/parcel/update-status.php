<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Parcel */

$this->title = 'Update Parcel Status: ' . $model->tracking_number;
$this->params['breadcrumbs'][] = ['label' => 'Parcels Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Parcel: ' . $model->tracking_number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parcel-update-status">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'status')->dropDownList($model::getStatusLabels(), ['prompt' => 'Select status']) ?>
    <div class="form-group mt-3">
        <?= Html::submitButton('Update Status', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div> 