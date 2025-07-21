<?php

use yii\db\Migration;

class m250801_000003_add_status_to_booking extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%booking}}', 'status', $this->string(20)->notNull()->defaultValue('pending')->comment('Booking status: pending, confirmed, cancelled, completed'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%booking}}', 'status');
    }
} 