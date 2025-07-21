<?php

use yii\db\Migration;

class m250801_000006_add_payment_method_to_booking extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%booking}}', 'payment_method', $this->string(50)->null()->comment('Payment method used for booking'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%booking}}', 'payment_method');
    }
} 