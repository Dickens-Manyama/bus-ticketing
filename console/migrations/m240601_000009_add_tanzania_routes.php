<?php

use yii\db\Migration;

/**
 * Add major Tanzanian routes
 */
class m240601_000009_add_tanzania_routes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $routes = [
            // Dar es Salaam Routes
            ['Dar es Salaam', 'Arusha', 45000, 'active'],
            ['Dar es Salaam', 'Mwanza', 55000, 'active'],
            ['Dar es Salaam', 'Mbeya', 35000, 'active'],
            ['Dar es Salaam', 'Dodoma', 25000, 'active'],
            ['Dar es Salaam', 'Tanga', 15000, 'active'],
            ['Dar es Salaam', 'Morogoro', 12000, 'active'],
            ['Dar es Salaam', 'Iringa', 30000, 'active'],
            ['Dar es Salaam', 'Songea', 40000, 'active'],
            ['Dar es Salaam', 'Kigoma', 65000, 'active'],
            ['Dar es Salaam', 'Tabora', 45000, 'active'],
            ['Dar es Salaam', 'Shinyanga', 50000, 'active'],
            ['Dar es Salaam', 'Musoma', 60000, 'active'],
            ['Dar es Salaam', 'Bukoba', 70000, 'active'],
            ['Dar es Salaam', 'Lindi', 25000, 'active'],
            ['Dar es Salaam', 'Mtwara', 30000, 'active'],
            ['Dar es Salaam', 'Kigoma', 65000, 'active'],
            
            // Arusha Routes
            ['Arusha', 'Mwanza', 35000, 'active'],
            ['Arusha', 'Dodoma', 25000, 'active'],
            ['Arusha', 'Tanga', 20000, 'active'],
            ['Arusha', 'Kilimanjaro', 8000, 'active'],
            ['Arusha', 'Manyara', 12000, 'active'],
            
            // Mwanza Routes
            ['Mwanza', 'Dodoma', 30000, 'active'],
            ['Mwanza', 'Tabora', 20000, 'active'],
            ['Mwanza', 'Shinyanga', 15000, 'active'],
            ['Mwanza', 'Musoma', 12000, 'active'],
            ['Mwanza', 'Bukoba', 20000, 'active'],
            
            // Mbeya Routes
            ['Mbeya', 'Dodoma', 20000, 'active'],
            ['Mbeya', 'Iringa', 15000, 'active'],
            ['Mbeya', 'Songea', 12000, 'active'],
            ['Mbeya', 'Tabora', 25000, 'active'],
            
            // Dodoma Routes (Capital)
            ['Dodoma', 'Tanga', 20000, 'active'],
            ['Dodoma', 'Morogoro', 15000, 'active'],
            ['Dodoma', 'Iringa', 18000, 'active'],
            ['Dodoma', 'Tabora', 20000, 'active'],
            ['Dodoma', 'Shinyanga', 25000, 'active'],
            
            // Cross-border Routes
            ['Dar es Salaam', 'Nairobi', 80000, 'active'],
            ['Arusha', 'Nairobi', 35000, 'active'],
            ['Dar es Salaam', 'Kampala', 120000, 'active'],
            ['Mwanza', 'Kampala', 80000, 'active'],
            ['Dar es Salaam', 'Lusaka', 150000, 'active'],
            ['Mbeya', 'Lusaka', 120000, 'active'],
        ];

        foreach ($routes as $route) {
            $this->insert('{{%route}}', [
                'origin' => $route[0],
                'destination' => $route[1],
                'price' => $route[2],
                'status' => $route[3],
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
        $this->delete('{{%route}}', [
            'origin' => [
                'Dar es Salaam', 'Arusha', 'Mwanza', 'Mbeya', 'Dodoma'
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
        echo "m250629_182801_add_tanzania_routes cannot be reverted.\n";

        return false;
    }
    */
}
