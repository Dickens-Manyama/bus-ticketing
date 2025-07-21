<?php

use yii\db\Migration;

class m240601_000008_add_status_to_bus_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%bus}}', 'status', $this->string(20)->notNull()->defaultValue('active')->comment('Bus status: active, inactive, maintenance'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%bus}}', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->addColumn('{{%bus}}', 'status', $this->string(20)->notNull()->defaultValue('active')->comment('Bus status: active, inactive, maintenance'));
    }

    public function down()
    {
        $this->dropColumn('{{%bus}}', 'status');
    }
    */
}
