<?php

use yii\db\Migration;
use yii\helpers\Console;
use yii\helpers\StringHelper;

/**
 * Handles inserting default data including admin users and demo buses.
 */
class m240601_999999_insert_default_admin_users extends Migration
{
    public function safeUp()
    {
        $this->insertAdminUsers();
        $this->insertDemoBuses();
    }

    private function insertAdminUsers()
    {
        $time = time();
        $users = [
            [
                'username' => 'superadmin',
                'email' => 'super@admin.com',
                'role' => 'superadmin',
            ],
            [
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'role' => 'admin',
            ],
            [
                'username' => 'manager',
                'email' => 'manager@admin.com',
                'role' => 'manager',
            ],
            [
                'username' => 'staff',
                'email' => 'staff@admin.com',
                'role' => 'staff',
            ],
        ];
        $password = 'Admin@123';
        $security = Yii::$app->security;
        foreach ($users as $user) {
            // Check if user already exists
            $exists = (new \yii\db\Query())
                ->from('{{%user}}')
                ->where(['username' => $user['username']])
                ->exists();
            if (!$exists) {
                $this->insert('{{%user}}', [
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'password_hash' => $security->generatePasswordHash($password),
                    'auth_key' => $security->generateRandomString(),
                    'role' => $user['role'],
                    'status' => 10, // Active
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
        }
    }

    private function insertDemoBuses()
    {
        $time = time();
        $buses = [
            // Luxury
            [
                'type' => 'Luxury',
                'class' => 'luxury',
                'seating_config' => '1x2',
                'seat_count' => 30,
                'plate_number' => 'LUX-001',
                'description' => 'Luxury bus with premium amenities',
                'amenities' => json_encode(['AC', 'WiFi', 'Recliner Seats']),
                'image' => null,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'type' => 'Luxury',
                'class' => 'luxury',
                'seating_config' => '1x2',
                'seat_count' => 30,
                'plate_number' => 'LUX-002',
                'description' => 'Luxury bus with snacks and drinks',
                'amenities' => json_encode(['AC', 'Snacks', 'Drinks']),
                'image' => null,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            // Semi-Luxury
            [
                'type' => 'Semi-Luxury',
                'class' => 'semi_luxury',
                'seating_config' => '2x2',
                'seat_count' => 40,
                'plate_number' => 'SEMI-001',
                'description' => 'Semi-Luxury bus with comfortable seats',
                'amenities' => json_encode(['AC', 'Comfort Seats']),
                'image' => null,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'type' => 'Semi-Luxury',
                'class' => 'semi_luxury',
                'seating_config' => '2x2',
                'seat_count' => 40,
                'plate_number' => 'SEMI-002',
                'description' => 'Semi-Luxury bus with snacks',
                'amenities' => json_encode(['Snacks', 'Music']),
                'image' => null,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            // Middle Class
            [
                'type' => 'Middle Class',
                'class' => 'middle_class',
                'seating_config' => '2x3',
                'seat_count' => 50,
                'plate_number' => 'MID-001',
                'description' => 'Middle class bus with basic amenities',
                'amenities' => json_encode(['Fan', 'Music']),
                'image' => null,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'type' => 'Middle Class',
                'class' => 'middle_class',
                'seating_config' => '2x3',
                'seat_count' => 50,
                'plate_number' => 'MID-002',
                'description' => 'Middle class bus with extra legroom',
                'amenities' => json_encode(['Extra Legroom']),
                'image' => null,
                'created_at' => $time,
                'updated_at' => $time,
            ],
        ];
        
        // Insert demo buses
        foreach ($buses as $bus) {
            $this->insert('{{%bus}}', $bus);
        }
        
        // Fix specific semi luxury bus if it exists
        $this->update('{{%bus}}', [
            'class' => 'semi_luxury',
            'seating_config' => '2x2',
            'seat_count' => 40,
            'type' => 'Semi-Luxury',
            'description' => 'Semi-Luxury bus with comfortable seating',
            'amenities' => json_encode(['AC', 'Comfort Seats']),
            'updated_at' => time(),
        ], [
            'plate_number' => 'TZ68497F'
        ]);
        
        // Generate seats for all buses
        $allBuses = (new \yii\db\Query())
            ->from('{{%bus}}')
            ->all();
        foreach ($allBuses as $bus) {
            $existingSeats = (new \yii\db\Query())
                ->from('{{%seat}}')
                ->where(['bus_id' => $bus['id']])
                ->indexBy('seat_number')
                ->all();
            for ($i = 1; $i <= $bus['seat_count']; $i++) {
                if (!isset($existingSeats[$i])) {
                    $this->insert('{{%seat}}', [
                        'bus_id' => $bus['id'],
                        'seat_number' => $i,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);
                }
            }
        }
    }

    public function safeDown()
    {
        // Delete demo buses
        $this->delete('{{%bus}}', ['plate_number' => ['LUX-001', 'LUX-002', 'SEMI-001', 'SEMI-002', 'MID-001', 'MID-002']]);
        
        // Revert the specific bus fix
        $this->update('{{%bus}}', [
            'class' => 'middle_class',
            'seating_config' => '2x3',
            'seat_count' => 50,
            'type' => 'Middle Class',
            'description' => 'Middle class bus',
            'amenities' => json_encode(['Fan']),
            'updated_at' => time(),
        ], [
            'plate_number' => 'TZ68497F'
        ]);
        
        // Delete admin users
        $this->delete('{{%user}}', ['username' => ['superadmin', 'admin', 'manager', 'staff']]);
    }
} 