<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Bus */

$this->title = 'Create Bus';
$this->params['breadcrumbs'][] = ['label' => 'Buses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bus-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="bus-form">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <?= $form->field($model, 'type')->dropDownList([
            'Luxury' => 'Luxury',
            'Semi-Luxury' => 'Semi-Luxury',
            'Middle Class' => 'Middle Class',
        ], ['prompt' => 'Select Bus Type']) ?>
        <?= $form->field($model, 'plate_number')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'seat_count')->textInput(['readonly' => true]) ?>
        <?= $form->field($model, 'image')->fileInput() ?>
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div> 