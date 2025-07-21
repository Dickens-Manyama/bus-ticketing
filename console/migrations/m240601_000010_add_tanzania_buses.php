<?php

use yii\db\Migration;

/**
 * Add Tanzania buses for different classes
 */
class m240601_000010_add_tanzania_buses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $buses = [
            // Luxury Buses (30 seats each)
            ['Luxury', 'luxury', '1x2', 30, 'T 123 ABC', 'active', '/uploads/bus/luxury_bus_1.jpg'],
            ['Luxury', 'luxury', '1x2', 30, 'T 456 DEF', 'active', '/uploads/bus/luxury_bus_2.jpg'],
            ['Luxury', 'luxury', '1x2', 30, 'T 789 GHI', 'active', '/uploads/bus/luxury_bus_3.jpg'],
            
            // Semi-Luxury Buses (40 seats each)
            ['Semi-Luxury', 'semi_luxury', '2x2', 40, 'T 234 JKL', 'active', '/uploads/bus/semi_luxury_bus_1.jpg'],
            ['Semi-Luxury', 'semi_luxury', '2x2', 40, 'T 567 MNO', 'active', '/uploads/bus/semi_luxury_bus_2.jpg'],
            ['Semi-Luxury', 'semi_luxury', '2x2', 40, 'T 890 PQR', 'active', '/uploads/bus/semi_luxury_bus_3.jpg'],
            
            // Middle Class Buses (60 seats each)
            ['Middle Class', 'middle_class', '2x3', 60, 'T 345 STU', 'active', '/uploads/bus/middle_class_bus_1.jpg'],
            ['Middle Class', 'middle_class', '2x3', 60, 'T 678 VWX', 'active', '/uploads/bus/middle_class_bus_2.jpg'],
            ['Middle Class', 'middle_class', '2x3', 60, 'T 901 YZA', 'active', '/uploads/bus/middle_class_bus_3.jpg'],
        ];

        foreach ($buses as $bus) {
            $this->insert('{{%bus}}', [
                'type' => $bus[0],
                'class' => $bus[1],
                'seating_config' => $bus[2],
                'seat_count' => $bus[3],
                'plate_number' => $bus[4],
                'status' => $bus[5],
                'image' => $bus[6],
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%bus}}', [
            'plate_number' => [
                'T 123 ABC', 'T 456 DEF', 'T 789 GHI',
                'T 234 JKL', 'T 567 MNO', 'T 890 PQR',
                'T 345 STU', 'T 678 VWX', 'T 901 YZA'
            ]
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250629_182853_add_tanzania_buses cannot be reverted.\n";

        return false;
    }
    */
}
