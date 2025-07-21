<?php

use yii\db\Migration;

class m250801_000005_add_distance_departure_time_to_route extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%route}}', 'distance', $this->float()->notNull()->defaultValue(0)->comment('Route distance in kilometers'));
        $this->addColumn('{{%route}}', 'departure_time', $this->timestamp()->null()->comment('Scheduled departure time'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%route}}', 'distance');
        $this->dropColumn('{{%route}}', 'departure_time');
    }
} 