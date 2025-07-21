<?php
use yii\db\Migration;

class m240601_000001_create_bus_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%bus}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(32)->notNull(),
            'class' => $this->string(32)->notNull(),
            'seating_config' => $this->string(16)->notNull(),
            'seat_count' => $this->integer()->notNull(),
            'plate_number' => $this->string(32)->notNull()->unique(),
            'route_id' => $this->integer()->null(),
            'description' => $this->text()->null(),
            'amenities' => $this->text()->null(),
            'image' => $this->string()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        // Foreign key for route_id will be added in a separate migration after all tables exist
        // $this->addForeignKey(
        //     'fk-bus-route_id',
        //     '{{%bus}}',
        //     'route_id',
        //     '{{%route}}',
        //     'id',
        //     'SET NULL',
        //     'CASCADE'
        // );
        // Add index for better performance
        $this->createIndex('idx-bus-route_id', '{{%bus}}', 'route_id');
    }

    public function safeDown()
    {
        // $this->dropForeignKey('fk-bus-route_id', '{{%bus}}');
        $this->dropIndex('idx-bus-route_id', '{{%bus}}');
        $this->dropTable('{{%bus}}');
    }
} 