<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Route */

$this->title = 'Update Route: ' . $model->origin . ' → ' . $model->destination;
$this->params['breadcrumbs'][] = ['label' => 'Routes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="route-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'origin')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'destination')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'price')->textInput(['type' => 'number', 'min' => 0]) ?>
        <?= $form->field($model, 'distance')->textInput(['type' => 'number', 'min' => 0, 'step' => '0.1', 'placeholder' => 'Distance in kilometers']) ?>
        <?= $form->field($model, 'departure_time')->input('time') ?>
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div> 