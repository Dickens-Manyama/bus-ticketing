<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $data array */

$this->title = 'Dashboard Debug';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4><i class="bi bi-bug"></i> Dashboard Debug Information</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Application Information:</h5>
                        <ul>
                            <li><strong>App ID:</strong> <?= Html::encode($data['app_id']) ?></li>
                            <li><strong>User Guest:</strong> <?= $data['user_guest'] ? 'Yes' : 'No' ?></li>
                            <li><strong>User Identity:</strong> <?= Html::encode($data['user_identity']) ?></li>
                            <li><strong>User Role:</strong> <?= Html::encode($data['user_role']) ?></li>
                            <li><strong>User ID:</strong> <?= Html::encode($data['user_id']) ?></li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h5>URL Information:</h5>
                        <ul>
                            <li><strong>Current URL:</strong> <?= Html::encode($data['current_url']) ?></li>
                            <li><strong>Base URL:</strong> <?= Html::encode($data['base_url']) ?></li>
                            <li><strong>Home URL:</strong> <?= Html::encode($data['home_url']) ?></li>
                            <li><strong>Default Route:</strong> <?= Html::encode($data['default_route']) ?></li>
                        </ul>
                    </div>

                    <div class="alert alert-<?= $data['database_connected'] ? 'success' : 'danger' ?>">
                        <h5>Database Status:</h5>
                        <ul>
                            <li><strong>Connected:</strong> <?= $data['database_connected'] ? 'Yes' : 'No' ?></li>
                            <?php if (isset($data['database_error'])): ?>
                                <li><strong>Error:</strong> <?= Html::encode($data['database_error']) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h6><i class="bi bi-link"></i> Quick Links</h6>
                                </div>
                                <div class="card-body">
                                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/dashboard/index'])) ?>" class="btn btn-primary mb-2 w-100">
                                        <i class="bi bi-speedometer2"></i> Go to Dashboard
                                    </a>
                                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/site/logout'])) ?>" class="btn btn-secondary mb-2 w-100">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h6><i class="bi bi-exclamation-triangle"></i> Troubleshooting</h6>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Check if the user exists in the database</li>
                                        <li>Verify the user role is correct</li>
                                        <li>Check if the dashboard route is accessible</li>
                                        <li>Verify URL rewriting is working</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 