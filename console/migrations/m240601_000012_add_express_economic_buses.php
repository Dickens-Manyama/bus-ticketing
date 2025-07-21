<?php

use yii\db\Migration;

/**
 * Add 4 Express and 4 Economic buses
 */
class m240601_000012_add_express_economic_buses extends Migration
{
    public function safeUp()
    {
        // Get route IDs for assignment
        $routes = $this->getRoutes();
        
        // Express Buses (Express class, 2x2 seating, 44 seats)
        $expressBuses = [
            [
                'type' => 'Express',
                'class' => 'express',
                'seating_config' => '2x2',
                'seat_count' => 44,
                'plate_number' => 'T 111 AAA',
                'route_key' => 'Dar es Salaam|Arusha',
                'description' => 'Premium express bus with luxury amenities',
                'amenities' => 'WiFi, USB Charging, Reclining Seats, Air Conditioning, Entertainment System',
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'type' => 'Express',
                'class' => 'express',
                'seating_config' => '2x2',
                'seat_count' => 44,
                'plate_number' => 'T 222 BBB',
                'route_key' => 'Dar es Salaam|Mwanza',
                'description' => 'High-speed express bus with premium comfort',
                'amenities' => 'WiFi, USB Charging, Reclining Seats, Air Conditioning, Entertainment System',
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'type' => 'Express',
                'class' => 'express',
                'seating_config' => '2x2',
                'seat_count' => 44,
                'plate_number' => 'T 333 CCC',
                'route_key' => 'Dar es Salaam|Mbeya',
                'description' => 'Express luxury bus for long-distance travel',
                'amenities' => 'WiFi, USB Charging, Reclining Seats, Air Conditioning, Entertainment System',
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'type' => 'Express',
                'class' => 'express',
                'seating_config' => '2x2',
                'seat_count' => 44,
                'plate_number' => 'T 444 DDD',
                'route_key' => 'Dar es Salaam|Dodoma',
                'description' => 'Premium express service with luxury features',
                'amenities' => 'WiFi, USB Charging, Reclining Seats, Air Conditioning, Entertainment System',
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];

        // Economic Buses (Economy class, 3x2 seating, 60 seats)
        $economicBuses = [
            [
                'type' => 'Economic',
                'class' => 'economy',
                'seating_config' => '3x2',
                'seat_count' => 60,
                'plate_number' => 'T 555 EEE',
                'route_key' => 'Dar es Salaam|Tanga',
                'description' => 'Affordable economic bus service',
                'amenities' => 'Air Conditioning, Basic Seating',
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'type' => 'Economic',
                'class' => 'economy',
                'seating_config' => '3x2',
                'seat_count' => 60,
                'plate_number' => 'T 666 FFF',
                'route_key' => 'Dar es Salaam|Morogoro',
                'description' => 'Cost-effective transportation option',
                'amenities' => 'Air Conditioning, Basic Seating',
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'type' => 'Economic',
                'class' => 'economy',
                'seating_config' => '3x2',
                'seat_count' => 60,
                'plate_number' => 'T 777 GGG',
                'route_key' => 'Dar es Salaam|Iringa',
                'description' => 'Budget-friendly bus service',
                'amenities' => 'Air Conditioning, Basic Seating',
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'type' => 'Economic',
                'class' => 'economy',
                'seating_config' => '3x2',
                'seat_count' => 60,
                'plate_number' => 'T 888 HHH',
                'route_key' => 'Dar es Salaam|Tabora',
                'description' => 'Economic transportation solution',
                'amenities' => 'Air Conditioning, Basic Seating',
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];

        // Insert Express Buses
        foreach ($expressBuses as $bus) {
            $bus['route_id'] = $routes[$bus['route_key']] ?? null;
            unset($bus['route_key']);
            $this->insert('{{%bus}}', $bus);
            $busId = $this->getDb()->getLastInsertID();
            $this->createSeats($busId, $bus['seat_count']);
        }

        // Insert Economic Buses
        foreach ($economicBuses as $bus) {
            $bus['route_id'] = $routes[$bus['route_key']] ?? null;
            unset($bus['route_key']);
            $this->insert('{{%bus}}', $bus);
            $busId = $this->getDb()->getLastInsertID();
            $this->createSeats($busId, $bus['seat_count']);
        }
    }

    public function safeDown()
    {
        // Remove the buses (seats will be removed automatically due to CASCADE)
        $this->delete('{{%bus}}', [
            'plate_number' => [
                'T 111 AAA', 'T 222 BBB', 'T 333 CCC', 'T 444 DDD', // Express
                'T 555 EEE', 'T 666 FFF', 'T 777 GGG', 'T 888 HHH'  // Economic
            ]
        ]);
    }

    /**
     * Get available routes for bus assignment
     */
    private function getRoutes()
    {
        $routes = [];
        $routeData = $this->getDb()->createCommand('SELECT id, origin, destination FROM {{%route}}')->queryAll();
        foreach ($routeData as $route) {
            $key = $route['origin'] . '|' . $route['destination'];
            $routes[$key] = $route['id'];
        }
        return $routes;
    }

    /**
     * Create seats for a bus
     */
    private function createSeats($busId, $seatCount)
    {
        for ($i = 1; $i <= $seatCount; $i++) {
            $this->insert('{{%seat}}', [
                'bus_id' => $busId,
                'seat_number' => $i,
                'status' => 'available',
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }
    }
} 