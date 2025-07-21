<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <span class="display-4 text-primary"><i class="bi bi-person-circle"></i></span>
                <h2 class="fw-bold mb-2">Sign In</h2>
                <p class="text-muted mb-0">Welcome back! Please login to your account.</p>
            </div>
            
            <!-- Admin Notice -->
            <div class="alert alert-info text-center mb-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Administrators:</strong> Please use the 
                <a href="http://localhost:8080" class="alert-link fw-bold">Admin Panel</a> 
                to access the backend system.
            </div>
            
            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Enter your username'])->label('Username') ?>
                        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Enter your password'])->label('Password') ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <?= $form->field($model, 'rememberMe')->checkbox(['class' => 'form-check-input'])->label('Remember Me') ?>
                            <div>
                                <small>
                                    <?= Html::a('Forgot password?', ['site/request-password-reset'], ['class' => 'link-secondary']) ?>
                                </small>
                            </div>
                        </div>
                        <div class="d-grid mb-2">
                            <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-lg', 'name' => 'login-button']) ?>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">Don't have an account? <?= Html::a('Sign Up', ['site/signup']) ?></small>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
