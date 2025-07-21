<?php
use yii\db\Migration;

class m240701_000001_create_backup_schedule_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('backup_schedule', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'plan' => $this->string(20)->notNull(), // daily, weekly, monthly, yearly, sixmonths
            'next_run' => $this->integer()->notNull(),
            'last_run' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk-backup_schedule-user_id', 'backup_schedule', 'user_id', 'user', 'id', 'CASCADE');
    }
    public function safeDown()
    {
        $this->dropForeignKey('fk-backup_schedule-user_id', 'backup_schedule');
        $this->dropTable('backup_schedule');
    }
} 