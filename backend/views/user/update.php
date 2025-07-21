<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Update User: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="user-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'status')->dropDownList([
            10 => 'Active',
            9 => 'Inactive',
            0 => 'Deleted',
        ]) ?>
        <?php if (Yii::$app->user->identity->isSuperAdmin()): ?>
            <?= $form->field($model, 'role')->dropDownList([
                'superadmin' => 'Super Admin',
                'admin' => 'Admin',
                'manager' => 'Manager',
                'staff' => 'Staff',
                'user' => 'User',
            ]) ?>
        <?php elseif (Yii::$app->user->identity->isManager()): ?>
            <?= $form->field($model, 'role')->dropDownList([
                'staff' => 'Staff',
            ], ['readonly' => true]) ?>
        <?php else: ?>
            <?= $form->field($model, 'role')->hiddenInput()->label(false) ?>
        <?php endif; ?>
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div> 