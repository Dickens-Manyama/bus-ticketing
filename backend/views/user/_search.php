<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Button;

/* @var $model backend\models\UserSearch */
?>

<div class="user-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'row g-3'],
    ]); ?>

    <div class="col-md-2">
        <?= $form->field($model, 'id')->textInput(['placeholder' => 'User ID']) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'username')->textInput(['placeholder' => 'Search username']) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'email')->textInput(['placeholder' => 'Search email']) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'status')->dropDownList([
            '' => 'All Statuses',
            \common\models\User::STATUS_ACTIVE => 'Active',
            \common\models\User::STATUS_INACTIVE => 'Inactive',
            \common\models\User::STATUS_DELETED => 'Deleted',
        ], ['class' => 'form-select']) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'role')->dropDownList([
            '' => 'All Roles',
            'superadmin' => 'Super Admin',
            'admin' => 'Admin',
            'manager' => 'Manager',
            'staff' => 'Staff',
            'user' => 'User',
        ], ['class' => 'form-select']) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'created_date_from')->textInput([
            'type' => 'date',
            'placeholder' => 'From Date'
        ]) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'created_date_to')->textInput([
            'type' => 'date',
            'placeholder' => 'To Date'
        ]) ?>
    </div>

    <div class="col-md-6">
        <div class="d-flex gap-2">
            <?= Html::submitButton('<i class="bi bi-search"></i> Search', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="bi bi-arrow-clockwise"></i> Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div> 