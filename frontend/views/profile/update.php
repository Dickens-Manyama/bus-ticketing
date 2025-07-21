<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Edit Profile';
$this->params['breadcrumbs'][] = ['label' => 'My Profile', 'url' => ['view']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <span class="display-4 text-primary"><i class="bi bi-pencil-square"></i></span>
                        <h2 class="fw-bold mb-2">Edit Profile</h2>
                        <p class="text-muted mb-0">Update your account details below.</p>
                    </div>
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Username']) ?>
                        <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Email']) ?>
                        <?= $form->field($model, 'profile_picture')->fileInput() ?>
                        <div class="d-grid gap-2 mt-3">
                            <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-lg']) ?>
                            <?= Html::a('Cancel', ['view'], ['class' => 'btn btn-secondary btn-lg']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div> 