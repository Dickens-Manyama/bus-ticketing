<?php

use yii\db\Migration;

class m250801_000001_add_bus_route_fk extends Migration
{
    public function safeUp()
    {
        $this->addForeignKey(
            'fk-bus-route_id',
            '{{%bus}}',
            'route_id',
            '{{%route}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-bus-route_id', '{{%bus}}');
    }
} 