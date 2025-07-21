<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Booking */

$this->title = 'Update Booking: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bookings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="booking-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'user_id')->textInput() ?>
        <?= $form->field($model, 'bus_id')->textInput() ?>
        <?= $form->field($model, 'route_id')->textInput() ?>
        <?= $form->field($model, 'seat_id')->textInput() ?>
        <?= $form->field($model, 'payment_info')->textInput(['maxlength' => true]) ?>
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div> 