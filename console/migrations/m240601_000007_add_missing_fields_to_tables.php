<?php

use yii\db\Migration;

class m240601_000007_add_missing_fields_to_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240601_000007_add_missing_fields_to_tables cannot be reverted.\n";
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240601_000007_add_missing_fields_to_tables cannot be reverted.\n";
        return false;
    }
    */
}
