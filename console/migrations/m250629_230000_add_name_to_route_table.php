<?php

use yii\db\Migration;

/**
 * Handles adding 'name' column to table `route`.
 */
class m250629_230000_add_name_to_route_table extends Migration
{
    public function safeUp()
    {
        // Step 1: Add as nullable
        $this->addColumn('route', 'name', $this->string(64)->null());
        // Step 2: Fill for all existing rows
        $this->execute("UPDATE route SET name = CONCAT(origin, ' - ', destination)");
        // Step 3: Set NOT NULL
        $this->alterColumn('route', 'name', $this->string(64)->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('route', 'name');
    }
} 