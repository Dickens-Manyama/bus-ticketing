<?php
use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\BackupSchedule;

$this->title = 'Backup Schedules';

// List backup files
$backupDir = Yii::getAlias('@backend/web/backups');
$backupFiles = [];
if (is_dir($backupDir)) {
    foreach (scandir($backupDir) as $file) {
        if (preg_match('/\.sql$/', $file)) {
            $backupFiles[] = $file;
        }
    }
    rsort($backupFiles);
}
?>
<?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
        <?= Html::encode($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endforeach; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::a('<i class="bi bi-plus-circle"></i> Create New Backup Plan', ['create'], ['class' => 'btn btn-success']) ?>
</div>
<?= \yii\widgets\ListView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels' => $schedules,
        'pagination' => [ 'pageSize' => 10 ],
    ]),
    'itemView' => function($model) {
        return '<div class="card mb-2"><div class="card-body d-flex justify-content-between align-items-center">'
            . '<div><b>Plan:</b> ' . Html::encode(BackupSchedule::getPlanOptions()[$model->plan] ?? $model->plan)
            . '<br><b>Next Run:</b> ' . date('Y-m-d H:i', $model->next_run)
            . '<br><b>Last Run:</b> ' . ($model->last_run ? date('Y-m-d H:i', $model->last_run) : '-') . '</div>'
            . Html::a('<i class="bi bi-trash"></i> Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-sm',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this schedule?',
                    'method' => 'post',
                ],
            ])
            . '</div></div>';
    },
    'emptyText' => '<div class="alert alert-info">No backup schedules found.</div>',
    'layout' => '{items}{pager}',
]) ?>

<h2 class="mt-5 mb-3">Backup Files</h2>
<?php if ($backupFiles): ?>
    <div class="list-group mb-4">
        <?php foreach ($backupFiles as $file): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-text"></i> <?= Html::encode($file) ?></span>
                <?= Html::a('<i class="bi bi-download"></i> Download', Yii::getAlias('@web/backups/' . $file), [
                    'class' => 'btn btn-outline-primary btn-sm',
                    'download' => true,
                    'target' => '_blank',
                ]) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">No backup files found.</div>
<?php endif; ?> 