<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $messages common\models\Message[] */

$this->title = 'Inbox';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-inbox">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('Compose Message', ['compose'], ['class' => 'btn btn-success']) ?></p>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>From</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Attachment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($messages as $msg): ?>
            <tr<?= $msg->is_read ? '' : ' style="font-weight:bold;"' ?>>
                <td><?= Html::encode($msg->sender ? $msg->sender->username : 'Unknown') ?></td>
                <td><?= Html::a(Html::encode($msg->subject), ['view', 'id' => $msg->id]) ?></td>
                <td><?= date('Y-m-d H:i', $msg->created_at) ?></td>
                <td>
                    <?php if ($msg->attachment_path): ?>
                        <a href="<?= Url::to(['download', 'id' => $msg->id]) ?>" target="_blank">Download</a>
                    <?php endif; ?>
                </td>
                <td>
                    <?= Html::a('Delete', ['delete', 'id' => $msg->id], [
                        'class' => 'btn btn-danger btn-sm',
                        'data-method' => 'post',
                        'data-confirm' => 'Are you sure you want to delete this message?',
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div> 