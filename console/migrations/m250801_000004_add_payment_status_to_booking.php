<?php

use yii\db\Migration;

class m250801_000004_add_payment_status_to_booking extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%booking}}', 'payment_status', $this->string(20)->notNull()->defaultValue('pending')->comment('Payment status: pending, paid, failed, refunded'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%booking}}', 'payment_status');
    }
} 