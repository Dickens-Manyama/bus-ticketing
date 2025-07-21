<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;
use common\models\Message;

class MsgController extends Controller
{
    public function actionTestInsert()
    {
        $users = User::find()->where(['role' => ['superadmin', 'admin', 'manager', 'staff']])->all();
        if (count($users) < 2) {
            echo "Not enough admin/staff/manager users to create test messages.\n";
            return;
        }
        $count = 0;
        foreach ($users as $sender) {
            foreach ($users as $recipient) {
                if ($sender->id !== $recipient->id) {
                    $msg = new Message();
                    $msg->sender_id = $sender->id;
                    $msg->recipient_id = $recipient->id;
                    $msg->subject = "Test Message from {$sender->username} to {$recipient->username}";
                    $msg->content = "This is a test message.";
                    $msg->is_read = 0;
                    $msg->created_at = time();
                    $msg->updated_at = time();
                    if ($msg->save(false)) {
                        $count++;
                    }
                }
            }
        }
        echo "Inserted $count test messages between admin/staff/manager users.\n";
    }
} 