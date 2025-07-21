<?php
use yii\db\Migration;

class m240601_000004_create_booking_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%booking}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'bus_id' => $this->integer()->notNull(),
            'seat_id' => $this->integer()->notNull(),
            'route_id' => $this->integer()->notNull(),
            'payment_info' => $this->string()->null(),
            'receipt' => $this->string()->null(),
            'qr_code' => $this->string()->null(),
            'ticket_status' => $this->string(20)->notNull()->defaultValue('active'), // active, expired, used
            'scanned_at' => $this->integer()->null(),
            'scanned_by' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk-booking-user_id', '{{%booking}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-booking-bus_id', '{{%booking}}', 'bus_id', '{{%bus}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-booking-seat_id', '{{%booking}}', 'seat_id', '{{%seat}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-booking-route_id', '{{%booking}}', 'route_id', '{{%route}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-booking-scanned_by', '{{%booking}}', 'scanned_by', '{{%user}}', 'id', 'SET NULL');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-booking-user_id', '{{%booking}}');
        $this->dropForeignKey('fk-booking-bus_id', '{{%booking}}');
        $this->dropForeignKey('fk-booking-seat_id', '{{%booking}}');
        $this->dropForeignKey('fk-booking-route_id', '{{%booking}}');
        $this->dropForeignKey('fk-booking-scanned_by', '{{%booking}}');
        $this->dropTable('{{%booking}}');
    }
} 