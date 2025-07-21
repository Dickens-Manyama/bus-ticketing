<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Parcel */
/* @var $routes array */
/* @var $users array */

$this->title = 'Update Parcel: ' . $model->tracking_number;
$this->params['breadcrumbs'][] = ['label' => 'Parcels Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parcel-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'user_id')->dropDownList(
        \yii\helpers\ArrayHelper::map($users, 'id', 'username'),
        ['prompt' => 'Select user']
    ) ?>
    <?= $form->field($model, 'parcel_type')->textInput() ?>
    <?= $form->field($model, 'parcel_category')->textInput() ?>
    <?= $form->field($model, 'weight')->textInput() ?>
    <?= $form->field($model, 'route_id')->dropDownList(
        \yii\helpers\ArrayHelper::map($routes, 'id', 'name'),
        ['prompt' => 'Select route']
    ) ?>
    <?= $form->field($model, 'price')->textInput() ?>
    <?= $form->field($model, 'status')->textInput() ?>
    <?= $form->field($model, 'payment_status')->textInput() ?>
    <?= $form->field($model, 'payment_method')->textInput() ?>
    <?= $form->field($model, 'sender_name')->textInput() ?>
    <?= $form->field($model, 'sender_phone')->textInput() ?>
    <?= $form->field($model, 'sender_address')->textInput() ?>
    <?= $form->field($model, 'recipient_name')->textInput() ?>
    <?= $form->field($model, 'recipient_phone')->textInput() ?>
    <?= $form->field($model, 'recipient_address')->textInput() ?>
    <?= $form->field($model, 'departure_date')->input('date') ?>
    <div class="form-group mt-3">
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div> 