<?php

use yii\helpers\Html;
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
                        <?php if ($model->profile_picture && file_exists(Yii::getAlias('@frontend/web') . $model->profile_picture)): ?>
                            <img src="<?= $model->profile_picture ?>" alt="Profile Picture" 
                                 class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 150px; height: 150px;">
                                <i class="fas fa-user fa-4x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <small class="text-muted">Profile picture will be shown here</small>
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->username) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->email) ?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Role</label>
                                <input type="text" class="form-control" value="<?= ucfirst($model->role) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <input type="text" class="form-control" 
                                       value="<?= $model->status == 10 ? 'Active' : 'Inactive' ?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Created At</label>
                                <input type="text" class="form-control" 
                                       value="<?= Yii::$app->formatter->asDatetime($model->created_at) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Last Login</label>
                                <input type="text" class="form-control" 
                                       value="<?= $model->last_login ? Yii::$app->formatter->asDatetime($model->last_login) : 'Never' ?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="<?= Url::to(['profile/update']) ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="<?= Url::to(['booking/bus']) ?>" class="btn btn-outline-primary w-100">
                                <i class="fas fa-ticket-alt me-1"></i>Book Ticket
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="<?= Url::to(['booking/my-bookings']) ?>" class="btn btn-outline-info w-100">
                                <i class="fas fa-receipt me-1"></i>My Bookings
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($model->isAdmin() || $model->isSuperAdmin() || $model->isManager()): ?>
                    <div class="row mt-2">
                        <div class="col-md-6 mb-2">
                            <a href="<?= Url::to(['user/index']) ?>" class="btn btn-outline-warning w-100">
                                <i class="fas fa-users me-1"></i>Manage Users
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="http://192.168.100.76:8081" target="_blank" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-cog me-1"></i>Backend Admin
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-index .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    margin-bottom: 1rem;
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

#profile-picture-container img {
    border: 3px solid #dee2e6;
    transition: border-color 0.3s ease;
}

@media (max-width: 768px) {
    .profile-index .col-md-4 {
        margin-bottom: 1rem;
    }
}
</style> 