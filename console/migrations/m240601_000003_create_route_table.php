<?php
use yii\db\Migration;

class m240601_000003_create_route_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%route}}', [
            'id' => $this->primaryKey(),
            'origin' => $this->string(64)->notNull(),
            'destination' => $this->string(64)->notNull(),
            'price' => $this->decimal(10,2)->notNull(),
            'status' => $this->string()->defaultValue('pending'),
            'started_at' => $this->integer()->null(),
            'finished_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        
        // Add index for status field for better performance
        $this->createIndex('idx-route-status', '{{%route}}', 'status');
    }

    public function safeDown()
    {
        $this->dropIndex('idx-route-status', '{{%route}}');
        $this->dropTable('{{%route}}');
    }
} 