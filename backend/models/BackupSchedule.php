<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "backup_schedule".
 * You may need to adjust the table name and attributes as needed.
 */
class BackupSchedule extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'backup_schedule';
    }

    /**
     * Get available backup plan options
     * @return array
     */
    public static function getPlanOptions()
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
    }
} 