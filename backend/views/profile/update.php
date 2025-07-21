<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Edit Profile';
$this->params['breadcrumbs'][] = ['label' => 'Profile', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>

<div class="profile-update">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-person-gear me-2"></i>Edit Profile
                    </h4>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($profileForm, 'username')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($profileForm, 'email')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($profileForm, 'role')->textInput(['readonly' => true]) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton('<i class="bi bi-check-circle me-2"></i>Update Profile', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('<i class="bi bi-arrow-left me-2"></i>Back to Profile', ['index'], ['class' => 'btn btn-secondary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <?php if ($user->profile_picture): ?>
                            <img src="<?= $user->profile_picture ?>" alt="Profile Picture" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-2">
                        <strong>Username:</strong> <?= Html::encode($user->username) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Email:</strong> <?= Html::encode($user->email) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Role:</strong> <?= Html::encode($user->role) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Status:</strong> 
                        <span class="badge bg-<?= $user->status == 10 ? 'success' : 'danger' ?>">
                            <?= $user->status == 10 ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Last Login:</strong> 
                        <?= $user->last_login ? date('Y-m-d H:i:s', $user->last_login) : 'Never' ?>
                    </div>
                    <div class="mb-2">
                        <strong>Created:</strong> <?= date('Y-m-d H:i:s', $user->created_at) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 