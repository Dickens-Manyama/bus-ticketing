<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\BackupSchedule;

$this->title = 'Create Backup Plan';
?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="card mt-3">
    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'plan')->dropDownList(BackupSchedule::getPlanOptions(), ['prompt' => 'Select a backup plan']) ?>
        <div class="form-group mt-3">
            <?= Html::submitButton('Create Plan', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary ms-2']) ?>
        </div>
        <?php ActiveForm::end(); ?> 