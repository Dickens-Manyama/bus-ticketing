<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <span class="display-4 text-primary"><i class="bi bi-person-plus"></i></span>
                        <h2 class="fw-bold mb-2">Create Account</h2>
                        <p class="text-muted mb-0">Sign up to book your bus tickets online.</p>
                    </div>
                    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Choose a username'])->label('Username') ?>
                        <?= $form->field($model, 'email')->textInput(['placeholder' => 'Enter your email'])->label('Email') ?>
                        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Create a password'])->label('Password') ?>
                        <div class="d-grid mb-2">
                            <?= Html::submitButton('Sign Up', ['class' => 'btn btn-primary btn-lg', 'name' => 'signup-button']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            <div class="text-center mt-3">
                <span class="text-muted">Already have an account?</span> <?= Html::a('Login', ['site/login'], ['class' => 'fw-bold']) ?>
            </div>
        </div>
    </div>
</div>
