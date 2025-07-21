<?php

use yii\db\Migration;

/**
 * Class m240601_000020_create_parcel_table
 */
class m240601_000020_create_parcel_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%parcel}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'route_id' => $this->integer()->notNull(),
            'parcel_type' => $this->string(255)->notNull(),
            'sender_name' => $this->string(500)->notNull(),
            'sender_phone' => $this->string(500)->notNull(),
            'sender_address' => $this->string(500),
            'recipient_name' => $this->string(500)->notNull(),
            'recipient_phone' => $this->string(500)->notNull(),
            'recipient_address' => $this->string(500),
            'description' => $this->string(500),
            'weight' => $this->decimal(10, 2)->notNull(),
            'dimensions' => $this->decimal(10, 2),
            'price' => $this->decimal(10, 2)->notNull(),
            'status' => $this->string(255)->defaultValue('pending'),
            'tracking_number' => $this->string(255)->unique(),
            'qr_code' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'departure_date' => $this->integer(),
            'payment_status' => $this->string(255)->defaultValue('pending'),
            'payment_method' => $this->string(255),
            'notes' => $this->string(500),
        ]);

        // Add foreign key constraints
        $this->addForeignKey(
            'fk-parcel-user_id',
            '{{%parcel}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-parcel-route_id',
            '{{%parcel}}',
            'route_id',
            '{{%route}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Add indexes for better performance
        $this->createIndex('idx-parcel-user_id', '{{%parcel}}', 'user_id');
        $this->createIndex('idx-parcel-route_id', '{{%parcel}}', 'route_id');
        $this->createIndex('idx-parcel-status', '{{%parcel}}', 'status');
        $this->createIndex('idx-parcel-payment_status', '{{%parcel}}', 'payment_status');
        $this->createIndex('idx-parcel-tracking_number', '{{%parcel}}', 'tracking_number');
        $this->createIndex('idx-parcel-created_at', '{{%parcel}}', 'created_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk-parcel-user_id', '{{%parcel}}');
        $this->dropForeignKey('fk-parcel-route_id', '{{%parcel}}');

        // Drop indexes
        $this->dropIndex('idx-parcel-user_id', '{{%parcel}}');
        $this->dropIndex('idx-parcel-route_id', '{{%parcel}}');
        $this->dropIndex('idx-parcel-status', '{{%parcel}}');
        $this->dropIndex('idx-parcel-payment_status', '{{%parcel}}');
        $this->dropIndex('idx-parcel-tracking_number', '{{%parcel}}');
        $this->dropIndex('idx-parcel-created_at', '{{%parcel}}');

        // Drop table
        $this->dropTable('{{%parcel}}');
    }
} 