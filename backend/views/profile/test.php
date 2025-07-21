<?php

use yii\helpers\Html;

$this->title = 'Profile Test';
$this->params['breadcrumbs'][] = ['label' => 'Profile', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="profile-test">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                <i class="fas fa-cog me-2"></i>Profile System Test
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>User Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>User ID</th>
                            <td><?= $data['user_id'] ?></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td><?= $data['username'] ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= $data['email'] ?></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td><?= ucfirst($data['role']) ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge <?= $data['status'] == 10 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $data['status'] == 10 ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Last Login</th>
                            <td><?= $data['last_login'] ?></td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td><?= $data['created_at'] ?></td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td><?= $data['updated_at'] ?></td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h5>Profile Picture Status</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Profile Picture Path</th>
                            <td><?= $data['profile_picture'] ?: 'Not set' ?></td>
                        </tr>
                        <tr>
                            <th>File Exists</th>
                            <td>
                                <span class="badge <?= $data['profile_picture_exists'] ? 'bg-success' : 'bg-warning' ?>">
                                    <?= $data['profile_picture_exists'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Uploads Directory</th>
                            <td>
                                <span class="badge <?= $data['uploads_dir_exists'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $data['uploads_dir_exists'] ? 'Exists' : 'Missing' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Directory Writable</th>
                            <td>
                                <span class="badge <?= $data['uploads_dir_writable'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $data['uploads_dir_writable'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                    
                    <?php if ($data['profile_picture'] && $data['profile_picture_exists']): ?>
                        <div class="mt-3">
                            <h6>Current Profile Picture:</h6>
                            <img src="<?= $data['profile_picture'] ?>" alt="Profile Picture" 
                                 class="img-fluid rounded" style="max-width: 200px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-4">
                <h5>System Status</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card <?= $data['uploads_dir_exists'] ? 'border-success' : 'border-danger' ?>">
                            <div class="card-body text-center">
                                <i class="fas fa-folder fa-3x <?= $data['uploads_dir_exists'] ? 'text-success' : 'text-danger' ?> mb-3"></i>
                                <h6>Uploads Directory</h6>
                                <p class="mb-0"><?= $data['uploads_dir_exists'] ? 'Ready' : 'Missing' ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card <?= $data['uploads_dir_writable'] ? 'border-success' : 'border-danger' ?>">
                            <div class="card-body text-center">
                                <i class="fas fa-edit fa-3x <?= $data['uploads_dir_writable'] ? 'text-success' : 'text-danger' ?> mb-3"></i>
                                <h6>Directory Permissions</h6>
                                <p class="mb-0"><?= $data['uploads_dir_writable'] ? 'Writable' : 'Read-only' ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card <?= $data['profile_picture_exists'] ? 'border-success' : 'border-warning' ?>">
                            <div class="card-body text-center">
                                <i class="fas fa-user-circle fa-3x <?= $data['profile_picture_exists'] ? 'text-success' : 'text-warning' ?> mb-3"></i>
                                <h6>Profile Picture</h6>
                                <p class="mb-0"><?= $data['profile_picture_exists'] ? 'Uploaded' : 'Not set' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="<?= Yii::$app->urlManager->createUrl(['profile/index']) ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Profile
                </a>
                <a href="<?= Yii::$app->urlManager->createUrl(['dashboard/index']) ?>" class="btn btn-secondary">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </div>
</div> 