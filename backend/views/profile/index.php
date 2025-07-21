<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap5\Alert;
use yii\helpers\Url;

$this->title = 'My Profile';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="profile-index">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Picture Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-circle me-2"></i>Profile Picture
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div id="profile-picture-container">
                        <!-- Profile picture feature removed: property does not exist -->
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-4x text-muted"></i>
                        </div>
                    </div>
                    <!-- Upload form removed -->
                    <small class="text-muted">Supported formats: JPG, PNG, GIF (Max 2MB)</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Profile Information Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-edit me-2"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['profile/update']),
                        'method' => 'post',
                        'options' => ['class' => 'profile-form']
                    ]); ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($profileForm, 'username')->textInput(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($profileForm, 'email')->textInput(['class' => 'form-control', 'type' => 'email']) ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="<?= ucfirst($user->role) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" 
                                       value="<?= $user->status == 10 ? 'Active' : 'Inactive' ?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Created At</label>
                                <input type="text" class="form-control" 
                                       value="<?= Yii::$app->formatter->asDatetime($user->created_at) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Last Login</label>
                                <!-- Removed last_login display: property does not exist -->
                                <input type="text" class="form-control" value="Never" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Profile
                        </button>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            
            <!-- Change Password Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-key me-2"></i>Change Password
                    </h5>
                </div>
                <div class="card-body">
                    <?php $passwordForm = ActiveForm::begin([
                        'action' => Url::to(['profile/change-password']),
                        'method' => 'post',
                        'options' => ['class' => 'password-form']
                    ]); ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?= $passwordForm->field($changePasswordForm, 'currentPassword')->passwordInput(['class' => 'form-control']) ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?= $passwordForm->field($changePasswordForm, 'newPassword')->passwordInput(['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $passwordForm->field($changePasswordForm, 'confirmPassword')->passwordInput(['class' => 'form-control']) ?>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Password Requirements:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Minimum 6 characters</li>
                            <li>At least one uppercase letter</li>
                            <li>At least one lowercase letter</li>
                            <li>At least one number</li>
                        </ul>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-1"></i>Change Password
                        </button>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-index .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.profile-index .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.profile-index .form-control:read-only {
    background-color: #e9ecef;
}

.profile-index .btn {
    border-radius: 0.375rem;
}

.profile-index .alert {
    border-radius: 0.375rem;
}

#profile-picture-container img {
    border: 3px solid #dee2e6;
    transition: border-color 0.3s ease;
}

#profile-picture-container img:hover {
    border-color: #007bff;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle file selection for profile picture
    const fileInput = document.getElementById('profile_picture');
    const submitButton = document.getElementById('submitPicture');
    const container = document.getElementById('profile-picture-container');
    
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            submitButton.style.display = 'inline-block';
            
            // Preview the selected image
            const file = this.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Create new image element
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.className = 'img-fluid rounded-circle mb-3';
                newImg.style = 'width: 150px; height: 150px; object-fit: cover; border: 3px solid #28a745;';
                newImg.alt = 'Profile Picture Preview';
                
                // Replace container content
                container.innerHTML = '';
                container.appendChild(newImg);
            };
            
            reader.readAsDataURL(file);
        } else {
            submitButton.style.display = 'none';
        }
    });
    
    // Form validation
    const forms = document.querySelectorAll('.profile-form, .password-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Show success messages
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        setTimeout(function() {
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i><?= Yii::$app->session->getFlash('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.profile-index').insertBefore(alert, document.querySelector('.profile-index').firstChild);
        }, 100);
    <?php endif; ?>
    
    // Show error messages
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        setTimeout(function() {
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-exclamation-circle me-2"></i><?= Yii::$app->session->getFlash('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.profile-index').insertBefore(alert, document.querySelector('.profile-index').firstChild);
        }, 100);
    <?php endif; ?>
});
</script> 