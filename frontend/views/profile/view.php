<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'My Profile';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <?php if ($model->profile_picture): ?>
                            <img src="<?= Html::encode($model->profile_picture) ?>" alt="Profile Picture" class="rounded-circle mb-2" style="width:100px;height:100px;object-fit:cover;">
                        <?php else: ?>
                            <span class="display-4 text-secondary"><i class="bi bi-person-circle"></i></span>
                        <?php endif; ?>
                        <h2 class="fw-bold mb-1">Hello, <?= Html::encode($model->username) ?>!</h2>
                        <p class="text-muted mb-0">Manage your account details below.</p>
                    </div>
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'username',
                            'email',
                        ],
                        'options' => ['class' => 'table table-borderless mb-4'],
                    ]) ?>
                    <div class="d-grid gap-2">
                        <?= Html::a('Edit Profile', ['update'], ['class' => 'btn btn-primary btn-lg']) ?>
                        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-grid'])
                            . Html::submitButton('<i class="fas fa-sign-out-alt me-2"></i>Logout', ['class' => 'btn btn-outline-danger btn-lg'])
                            . Html::endForm() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 