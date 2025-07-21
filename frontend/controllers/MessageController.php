<?php
namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use common\models\Message;
use common\models\User;

class MessageController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionInbox()
    {
        $userId = Yii::$app->user->id;
        $messages = Message::find()->where(['recipient_id' => $userId])->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('inbox', [
            'messages' => $messages,
        ]);
    }

    public function actionSent()
    {
        $userId = Yii::$app->user->id;
        $messages = Message::find()->where(['sender_id' => $userId])->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('sent', [
            'messages' => $messages,
        ]);
    }

    public function actionCompose()
    {
        $model = new Message();
        $model->sender_id = Yii::$app->user->id;
        $roles = ['superadmin', 'admin', 'manager', 'staff'];
        $recipients = User::find()->where(['role' => $roles])->andWhere(['!=', 'id', $model->sender_id])->all();
        $recipientOptions = ['all' => 'All Administrators'];
        foreach ($recipients as $user) {
            $recipientOptions[$user->id] = $user->username;
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->recipient_id === 'all') {
                $sent = 0;
                foreach ($recipients as $user) {
                    $msg = new Message();
                    $msg->sender_id = $model->sender_id;
                    $msg->recipient_id = $user->id;
                    $msg->subject = $model->subject;
                    $msg->content = $model->content;
                    $msg->is_read = 0;
                    $msg->created_at = time();
                    $msg->updated_at = time();
                    if ($model->attachmentFile) {
                        $msg->attachmentFile = $model->attachmentFile;
                        $uploadDir = Yii::getAlias('@frontend/web/uploads/messages/');
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $filename = uniqid() . '_' . $msg->attachmentFile->baseName . '.' . $msg->attachmentFile->extension;
                        $msg->attachmentFile->saveAs($uploadDir . $filename);
                        $msg->attachment_path = '/uploads/messages/' . $filename;
                        $msg->attachment_name = $msg->attachmentFile->name;
                    }
                    if ($msg->save(false)) {
                        $sent++;
                    }
                }
                Yii::$app->session->setFlash('success', "Message sent to $sent administrators.");
                return $this->redirect(['sent']);
            } else {
                $model->attachmentFile = UploadedFile::getInstance($model, 'attachmentFile');
                if ($model->attachmentFile) {
                    $uploadDir = Yii::getAlias('@frontend/web/uploads/messages/');
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $filename = uniqid() . '_' . $model->attachmentFile->baseName . '.' . $model->attachmentFile->extension;
                    $model->attachmentFile->saveAs($uploadDir . $filename);
                    $model->attachment_path = '/uploads/messages/' . $filename;
                    $model->attachment_name = $model->attachmentFile->name;
                }
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Message sent successfully.');
                    return $this->redirect(['sent']);
                }
            }
        }
        return $this->render('compose', [
            'model' => $model,
            'recipients' => $recipients,
            'recipientOptions' => $recipientOptions,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model->recipient_id == Yii::$app->user->id && !$model->is_read) {
            $model->is_read = 1;
            $model->save(false, ['is_read']);
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->sender_id == Yii::$app->user->id || $model->recipient_id == Yii::$app->user->id) {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Message deleted.');
        }
        return $this->redirect(['inbox']);
    }

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $filePath = Yii::getAlias('@app/web') . $model->attachment_path;
        if ($model->attachment_path && is_file($filePath)) {
            return Yii::$app->response->sendFile($filePath, $model->attachment_name);
        }
        throw new NotFoundHttpException('Attachment not found.');
    }

    public function actionIndex($chatWith = null, $box = 'inbox')
    {
        $userId = Yii::$app->user->id;
        $roles = ['superadmin', 'admin', 'manager', 'staff'];
        $admins = User::find()->where(['role' => $roles])->andWhere(['!=', 'id', $userId])->all();
        $unreadCount = Message::find()->where(['recipient_id' => $userId, 'is_read' => 0])->count();
        $inbox = $sent = $archive = $conversation = [];
        $selectedUser = null;

        if ($box === 'inbox') {
            $inbox = Message::find()->where(['recipient_id' => $userId])->orderBy(['created_at' => SORT_DESC])->all();
        } elseif ($box === 'sent') {
            $sent = Message::find()->where(['sender_id' => $userId])->orderBy(['created_at' => SORT_DESC])->all();
        } elseif ($box === 'archive') {
            $archive = [];
        }

        if ($chatWith) {
            $selectedUser = User::findOne($chatWith);
            if ($selectedUser) {
                $conversation = Message::find()
                    ->where(['or',
                        ['and', ['sender_id' => $userId, 'recipient_id' => $selectedUser->id]],
                        ['and', ['sender_id' => $selectedUser->id, 'recipient_id' => $userId]]
                    ])
                    ->orderBy(['created_at' => SORT_ASC])
                    ->all();
            }
        }

        return $this->render('index', [
            'admins' => $admins,
            'inbox' => $inbox,
            'sent' => $sent,
            'archive' => $archive,
            'unreadCount' => $unreadCount,
            'box' => $box,
            'selectedUser' => $selectedUser,
            'conversation' => $conversation,
        ]);
    }

    public function actionSendChat($to)
    {
        $userId = Yii::$app->user->id;
        $recipient = User::findOne($to);
        if (!$recipient) {
            Yii::$app->session->setFlash('error', 'Recipient not found.');
            return $this->redirect(['index', 'chatWith' => $to]);
        }
        $model = new Message();
        $model->sender_id = $userId;
        $model->recipient_id = $recipient->id;
        if ($model->load(Yii::$app->request->post())) {
            $model->subject = 'Chat';
            $model->attachmentFile = UploadedFile::getInstance($model, 'attachmentFile');
            if ($model->attachmentFile) {
                $uploadDir = Yii::getAlias('@app/web/uploads/messages/');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = uniqid() . '_' . $model->attachmentFile->baseName . '.' . $model->attachmentFile->extension;
                $model->attachmentFile->saveAs($uploadDir . $filename);
                $model->attachment_path = '/uploads/messages/' . $filename;
                $model->attachment_name = $model->attachmentFile->name;
            }
            if ($model->save()) {
                return $this->redirect(['index', 'chatWith' => $recipient->id]);
            } else {
                Yii::$app->session->setFlash('error', implode('; ', $model->getFirstErrors()));
            }
        }
        return $this->redirect(['index', 'chatWith' => $recipient->id]);
    }

    public function actionFetchChat($chatWith)
    {
        Yii::setAlias('@web', Yii::$app->request->getBaseUrl());
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;
        $selectedUser = User::findOne($chatWith);
        if (!$selectedUser) {
            return ['success' => false, 'messages' => []];
        }
        $conversation = Message::find()
            ->where(['or',
                ['and', ['sender_id' => $userId, 'recipient_id' => $selectedUser->id]],
                ['and', ['sender_id' => $selectedUser->id, 'recipient_id' => $userId]]
            ])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
        $messages = [];
        foreach ($conversation as $msg) {
            $messages[] = [
                'id' => $msg->id,
                'sender' => $msg->sender ? $msg->sender->username : 'Unknown',
                'sender_id' => $msg->sender_id,
                'content' => $msg->content,
                'created_at' => date('Y-m-d H:i', $msg->created_at),
                'attachment_path' => $msg->attachment_path,
                'attachment_name' => $msg->attachment_name,
            ];
        }
        return ['success' => true, 'messages' => $messages, 'currentUserId' => $userId];
    }

    public function actionMarkAllRead()
    {
        $userId = Yii::$app->user->id;
        \common\models\Message::updateAll(['is_read' => 1], ['recipient_id' => $userId, 'is_read' => 0]);
        Yii::$app->session->setFlash('success', 'All messages marked as read.');
        return $this->redirect(['index', 'box' => 'inbox']);
    }

    protected function findModel($id)
    {
        $model = Message::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('The requested message does not exist.');
        }
        return $model;
    }
} 