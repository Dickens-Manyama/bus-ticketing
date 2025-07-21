<?php
use yii\db\Migration;

class m240601_000002_create_seat_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%seat}}', [
            'id' => $this->primaryKey(),
            'bus_id' => $this->integer()->notNull(),
            'seat_number' => $this->string(8)->notNull(),
            'status' => $this->string(16)->notNull()->defaultValue('available'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk-seat-bus_id', '{{%seat}}', 'bus_id', '{{%bus}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-seat-bus_id', '{{%seat}}');
        $this->dropTable('{{%seat}}');
    }
} 