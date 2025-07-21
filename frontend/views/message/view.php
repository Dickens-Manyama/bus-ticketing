<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = Html::encode($model->subject);
$this->params['breadcrumbs'][] = ['label' => 'Inbox', 'url' => ['inbox']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-view">
    <h1><?= Html::encode($model->subject) ?></h1>
    <p><strong>From:</strong> <?= Html::encode($model->sender ? $model->sender->username : 'Unknown') ?></p>
    <p><strong>To:</strong> <?= Html::encode($model->recipient ? $model->recipient->username : 'Unknown') ?></p>
    <p><strong>Date:</strong> <?= date('Y-m-d H:i', $model->created_at) ?></p>
    <p><strong>Message:</strong><br><?= nl2br(Html::encode($model->content)) ?></p>
    <?php if ($model->attachment_path): ?>
        <p><strong>Attachment:</strong> <a href="<?= Url::to(['download', 'id' => $model->id]) ?>" target="_blank">Download</a></p>
    <?php endif; ?>
    <p><?= Html::a('Back to Inbox', ['inbox'], ['class' => 'btn btn-secondary']) ?></p>
</div> 