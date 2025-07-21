<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $admins common\models\User[] */
/* @var $inbox common\models\Message[] */
/* @var $sent common\models\Message[] */
/* @var $archive array */
/* @var $unreadCount int */
/* @var $box string */
/* @var $selectedUser common\models\User|null */
/* @var $conversation common\models\Message[] */

$this->title = 'Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-dashboard row" style="min-height: 500px;">
    <div class="col-12 mb-3">
        <ul class="nav nav-pills small-menu">
            <li class="nav-item">
                <a class="nav-link<?= $box === 'inbox' ? ' active' : '' ?>" href="<?= Url::to(['index', 'box' => 'inbox']) ?>">Inbox <?php if ($unreadCount > 0): ?><span class="badge bg-danger"><?= $unreadCount ?></span><?php endif; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?= $box === 'archive' ? ' active' : '' ?>" href="<?= Url::to(['index', 'box' => 'archive']) ?>">Archive</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?= $box === 'sent' ? ' active' : '' ?>" href="<?= Url::to(['index', 'box' => 'sent']) ?>">Sentbox</a>
            </li>
        </ul>
    </div>
    <div class="col-md-3 border-end" style="max-width: 260px;">
        <div class="list-group mb-3">
            <div class="list-group-item active">Administrators</div>
            <?php foreach ($admins as $admin): ?>
                <a href="<?= Url::to(['index', 'chatWith' => $admin->id]) ?>" class="list-group-item list-group-item-action<?= ($selectedUser && $selectedUser->id == $admin->id) ? ' bg-info text-white' : '' ?>">
                    <i class="bi bi-person-circle"></i> <?= Html::encode($admin->username) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-9">
        <?php if ($selectedUser): ?>
            <?php $model = new \common\models\Message(); ?>
            <div class="chatbox card">
                <div class="card-header bg-light">
                    <b>Chat with <?= Html::encode($selectedUser->username) ?></b>
                </div>
                <div class="card-body" style="height: 350px; overflow-y: auto; background: #f8f9fa;">
                    <?php if (empty($conversation)): ?>
                        <div class="text-muted">No messages yet. Start the conversation!</div>
                    <?php else: ?>
                        <?php foreach ($conversation as $msg): ?>
                            <div class="mb-2 text-<?= $msg->sender_id == Yii::$app->user->id ? 'end' : 'start' ?>">
                                <div class="d-inline-block p-2 rounded <?= $msg->sender_id == Yii::$app->user->id ? 'bg-primary text-white' : 'bg-white border' ?>">
                                    <div><small><?= Html::encode($msg->sender->username) ?> <span class="text-muted small"><?= date('Y-m-d H:i', $msg->created_at) ?></span></small></div>
                                    <div><?= nl2br(Html::encode($msg->content)) ?></div>
                                    <?php if ($msg->attachment_path): ?>
                                        <div><a href="<?= Url::to(['download', 'id' => $msg->id]) ?>" target="_blank">📎 <?= Html::encode($msg->attachment_name) ?></a></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white">
                    <?php $form = \yii\widgets\ActiveForm::begin([
                        'action' => ['send-chat', 'to' => $selectedUser->id],
                        'options' => ['enctype' => 'multipart/form-data', 'class' => 'd-flex align-items-center']
                    ]); ?>
                    <?= $form->errorSummary($model) ?>
                    <div class="flex-grow-1 me-2">
                        <?= $form->field($model, 'content')->textInput(['placeholder' => 'Type a message...', 'class' => 'form-control'])->label(false) ?>
                    </div>
                    <div class="me-2">
                        <?= $form->field($model, 'attachmentFile')->fileInput(['class' => 'form-control form-control-sm'])->label(false) ?>
                    </div>
                    <div>
                        <?= Html::submitButton('<i class="bi bi-send"></i>', ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
            <script>
            let lastMessageCount = <?= count($conversation) ?>;
            function fetchChat() {
                $.getJSON('<?= Url::to(['fetch-chat', 'chatWith' => $selectedUser->id]) ?>', function(data) {
                    if (data.success) {
                        let chatBody = $('.chatbox .card-body');
                        let html = '';
                        let currentUserId = data.currentUserId;
                        data.messages.forEach(function(msg) {
                            let align = msg.sender_id == currentUserId ? 'end' : 'start';
                            let bubbleClass = msg.sender_id == currentUserId ? 'bg-primary text-white' : 'bg-white border';
                            html += `<div class=\"mb-2 text-${align}\">`;
                            html += `<div class=\"d-inline-block p-2 rounded ${bubbleClass}\">`;
                            html += `<div><small>${msg.sender} <span class=\"text-muted small\">${msg.created_at}</span></small></div>`;
                            html += `<div>${msg.content.replace(/\n/g, '<br>')}</div>`;
                            if (msg.attachment_path) {
                                html += `<div><a href=\"${msg.attachment_path}\" target=\"_blank\">📎 ${msg.attachment_name}</a></div>`;
                            }
                            html += `</div></div>`;
                        });
                        chatBody.html(html);
                        // Scroll to bottom if new messages
                        if (data.messages.length > lastMessageCount) {
                            chatBody.scrollTop(chatBody[0].scrollHeight);
                            lastMessageCount = data.messages.length;
                        }
                    }
                });
            }
            setInterval(fetchChat, 2000);
            </script>
        <?php else: ?>
            <?php if ($box === 'inbox'): ?>
                <h5>Inbox</h5>
                <form method="post" action="<?= \yii\helpers\Url::to(['mark-all-read']) ?>">
                    <?= \yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                    <button type="submit" class="btn btn-sm btn-outline-success mb-2">Mark All as Read</button>
                </form>
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
                    <?php foreach ($inbox as $msg): ?>
                        <tr<?= $msg->is_read ? '' : ' style="font-weight:bold;background:#f8d7da;"' ?>>
                            <td><?= \yii\helpers\Html::encode($msg->sender ? $msg->sender->username : 'Unknown') ?></td>
                            <td><?= \yii\helpers\Html::a(\yii\helpers\Html::encode($msg->subject), ['view', 'id' => $msg->id]) ?></td>
                            <td><?= date('Y-m-d H:i', $msg->created_at) ?></td>
                            <td>
                                <?php if ($msg->attachment_path): ?>
                                    <a href="<?= \yii\helpers\Url::to(['download', 'id' => $msg->id]) ?>" target="_blank">Download</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= \yii\helpers\Html::a('Delete', ['delete', 'id' => $msg->id], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data-method' => 'post',
                                    'data-confirm' => 'Are you sure you want to delete this message?',
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($box === 'sent'): ?>
                <h5>Sentbox</h5>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>To</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Attachment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sent as $msg): ?>
                        <tr>
                            <td><?= Html::encode($msg->recipient ? $msg->recipient->username : 'Unknown') ?></td>
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
            <?php elseif ($box === 'archive'): ?>
                <h5>Archive</h5>
                <div class="text-muted">No archived messages yet.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div> 