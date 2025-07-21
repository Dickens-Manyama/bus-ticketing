<?php

use yii\db\Migration;

/**
 * Handles the creation of communication tables (message and notification).
 */
class m240601_000006_create_communication_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Only create message table if it doesn't exist
        if (!$this->db->schema->getTableSchema('{{%message}}')) {
            $this->createMessageTable();
        }
        
        // Only create notification table if it doesn't exist
        if (!$this->db->schema->getTableSchema('{{%notification}}')) {
            $this->createNotificationTable();
        }
    }

    private function createMessageTable()
    {
        $this->createTable('{{%message}}', [
            'id' => $this->primaryKey(),
            'sender_id' => $this->integer()->notNull(),
            'recipient_id' => $this->integer()->notNull(),
            'subject' => $this->string(255)->notNull(),
            'content' => $this->text()->notNull(),
            'attachment_path' => $this->string(500)->null(),
            'attachment_name' => $this->string(500)->null(),
            'is_read' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Add indexes for better performance
        $this->createIndex('idx-message-sender_id', '{{%message}}', 'sender_id');
        $this->createIndex('idx-message-recipient_id', '{{%message}}', 'recipient_id');
        $this->createIndex('idx-message-is_read', '{{%message}}', 'is_read');
        $this->createIndex('idx-message-created_at', '{{%message}}', 'created_at');

        // Add foreign key constraints
        $this->addForeignKey(
            'fk-message-sender_id',
            '{{%message}}',
            'sender_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'fk-message-recipient_id',
            '{{%message}}',
            'recipient_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    private function createNotificationTable()
    {
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'sender_id' => $this->integer()->null(),
            'recipient_id' => $this->integer()->null(),
            'group' => $this->string(64)->null(),
            'title' => $this->string(255)->notNull(),
            'message' => $this->text()->notNull(),
            'type' => $this->string(64)->notNull(),
            'is_read' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Add indexes
        $this->createIndex('idx-notification-recipient_id', '{{%notification}}', 'recipient_id');
        $this->createIndex('idx-notification-group', '{{%notification}}', 'group');
        $this->createIndex('idx-notification-type', '{{%notification}}', 'type');
        $this->createIndex('fk-notification-sender', '{{%notification}}', 'sender_id');

        // Add foreign keys
        $this->addForeignKey('fk-notification-recipient', '{{%notification}}', 'recipient_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-notification-sender', '{{%notification}}', 'sender_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop notification table if it exists
        if ($this->db->schema->getTableSchema('{{%notification}}')) {
            $this->dropForeignKey('fk-notification-sender', '{{%notification}}');
            $this->dropForeignKey('fk-notification-recipient', '{{%notification}}');
            $this->dropTable('{{%notification}}');
        }
        
        // Drop message table if it exists
        if ($this->db->schema->getTableSchema('{{%message}}')) {
            $this->dropForeignKey('fk-message-recipient_id', '{{%message}}');
            $this->dropForeignKey('fk-message-sender_id', '{{%message}}');
            $this->dropIndex('idx-message-created_at', '{{%message}}');
            $this->dropIndex('idx-message-is_read', '{{%message}}');
            $this->dropIndex('idx-message-recipient_id', '{{%message}}');
            $this->dropIndex('idx-message-sender_id', '{{%message}}');
            $this->dropTable('{{%message}}');
        }
    }
}
