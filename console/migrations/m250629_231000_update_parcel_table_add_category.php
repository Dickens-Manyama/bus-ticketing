<?php

use yii\db\Migration;

/**
 * Handles updating the parcel table to replace dimensions with parcel_category.
 */
class m250629_231000_update_parcel_table_add_category extends Migration
{
    public function safeUp()
    {
        // Drop the dimensions column
        $this->dropColumn('parcel', 'dimensions');
        
        // Add the parcel_category column
        $this->addColumn('parcel', 'parcel_category', $this->string(64)->notNull()->defaultValue('other'));
    }

    public function safeDown()
    {
        // Drop the parcel_category column
        $this->dropColumn('parcel', 'parcel_category');
        
        // Add back the dimensions column
        $this->addColumn('parcel', 'dimensions', $this->string(255));
    }
} 