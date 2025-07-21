<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $data */

$this->title = 'Backend Debug Info';
?>

<div class="site-debug">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="card">
        <div class="card-header">
            <h5>Backend Status</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <td><strong>Application ID:</strong></td>
                    <td><?= Html::encode($data['app_id']) ?></td>
                </tr>
                <tr>
                    <td><strong>User Guest:</strong></td>
                    <td><?= $data['user_guest'] ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <td><strong>User Identity:</strong></td>
                    <td><?= Html::encode($data['user_identity']) ?></td>
                </tr>
                <tr>
                    <td><strong>User Role:</strong></td>
                    <td><?= Html::encode($data['user_role']) ?></td>
                </tr>
                <tr>
                    <td><strong>Database Connected:</strong></td>
                    <td><?= $data['database_connected'] ? 'Yes' : 'No' ?></td>
                </tr>
                <?php if (isset($data['database_error'])): ?>
                <tr>
                    <td><strong>Database Error:</strong></td>
                    <td class="text-danger"><?= Html::encode($data['database_error']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="btn btn-primary">Go to Login</a>
        <a href="<?= Yii::$app->urlManager->createUrl(['dashboard/index']) ?>" class="btn btn-success">Go to Dashboard</a>
    </div>
</div> 