<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "message".
 *
 * @property int $id
 * @property int $sender_id
 * @property int $recipient_id
 * @property string $subject
 * @property string $content
 * @property string|null $attachment_path
 * @property string|null $attachment_name
 * @property int $is_read
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $sender
 * @property User $recipient
 */
class Message extends ActiveRecord
{
    /** @var UploadedFile */
    public $attachmentFile;

    public static function tableName()
    {
        return 'message';
    }

    public function rules()
    {
        return [
            [['sender_id', 'recipient_id', 'subject', 'content'], 'required'],
            [['sender_id', 'recipient_id', 'is_read', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['subject'], 'string', 'max' => 255],
            [['attachment_path', 'attachment_name'], 'string', 'max' => 500],
            [['attachmentFile'], 'file', 'skipOnEmpty' => true, 'maxSize' => 1024 * 1024 * 10], // 10MB max
            [['recipient_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['recipient_id' => 'id']],
            [['sender_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['sender_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => 'Sender',
            'recipient_id' => 'Recipient',
            'subject' => 'Subject',
            'content' => 'Message',
            'attachment_path' => 'Attachment Path',
            'attachment_name' => 'Attachment',
            'is_read' => 'Read',
            'created_at' => 'Sent At',
            'updated_at' => 'Updated At',
            'attachmentFile' => 'Attachment',
        ];
    }

    public function getSender()
    {
        return $this->hasOne(User::class, ['id' => 'sender_id']);
    }

    public function getRecipient()
    {
        return $this->hasOne(User::class, ['id' => 'recipient_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = time();
                $this->is_read = 0;
            }
            $this->updated_at = time();
            return true;
        }
        return false;
    }
} 