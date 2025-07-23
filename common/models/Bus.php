<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bus".
 *
 * @property int $id
 * @property string $type
 * @property string $class
 * @property string $seating_config
 * @property int $seat_count
 * @property string|null $image
 * @property int $created_at
 * @property int $updated_at
 * @property string $plate_number
 * @property string $description
 * @property string $amenities
 *
 * @property Seat[] $seats
 * @property Booking[] $bookings
 * @property Route $route
 */
class Bus extends ActiveRecord
{
    const CLASS_LUXURY = 'luxury';
    const CLASS_SEMI_LUXURY = 'semi_luxury';
    const CLASS_MIDDLE_CLASS = 'middle_class';
    const CLASS_EXPRESS = 'express';
    const CLASS_ECONOMY = 'economy';

    const SEATING_1X2 = '1x2';
    const SEATING_2X2 = '2x2';
    const SEATING_2X3 = '2x3';

    public static function tableName()
    {
        return 'bus';
    }

    public function rules()
    {
        return [
            [['type', 'plate_number', 'seat_count', 'class', 'seating_config', 'created_at', 'updated_at'], 'required'],
            [['seat_count', 'created_at', 'updated_at'], 'integer'],
            [['type', 'plate_number', 'class', 'seating_config'], 'string', 'max' => 32],
            [['image'], 'string', 'max' => 255],
            [['description', 'amenities'], 'string'],
            [['class'], 'in', 'range' => [self::CLASS_LUXURY, self::CLASS_SEMI_LUXURY, self::CLASS_MIDDLE_CLASS, self::CLASS_EXPRESS, self::CLASS_ECONOMY]],
            [['seating_config'], 'in', 'range' => [self::SEATING_1X2, self::SEATING_2X2, self::SEATING_2X3]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'class' => 'Class',
            'seating_config' => 'Seating Configuration',
            'seat_count' => 'Seat Count',
            'image' => 'Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'plate_number' => 'Plate Number',
            'description' => 'Description',
            'amenities' => 'Amenities',
        ];
    }

    public function getSeats()
    {
        return $this->hasMany(Seat::class, ['bus_id' => 'id'])->orderBy([new \yii\db\Expression('CAST(seat_number AS UNSIGNED) ASC')]);
    }

    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['bus_id' => 'id']);
    }

    public function getRoute()
    {
        return $this->hasOne(Route::class, ['id' => 'route_id']);
    }

    public function getClassLabel()
    {
        $labels = [
            self::CLASS_LUXURY => 'Luxury',
            self::CLASS_SEMI_LUXURY => 'Semi-Luxury',
            self::CLASS_MIDDLE_CLASS => 'Middle Class',
            self::CLASS_EXPRESS => 'Express',
            self::CLASS_ECONOMY => 'Economy',
        ];
        return $labels[$this->class] ?? $this->class;
    }

    public function getSeatingConfigLabel()
    {
        $labels = [
            self::SEATING_1X2 => '1×2 (Single Aisle)',
            self::SEATING_2X2 => '2×2 (Double Aisle)',
            self::SEATING_2X3 => '2×3 (Triple Aisle)',
        ];
        return $labels[$this->seating_config] ?? $this->seating_config;
    }

    public function getAmenitiesArray()
    {
        if (empty($this->amenities) || !is_string($this->amenities)) {
            return [];
        }
        return json_decode($this->amenities, true) ?: [];
    }

    public function setAmenitiesArray($amenities)
    {
        $this->amenities = json_encode($amenities);
    }

    public function getClassColor()
    {
        $colors = [
            self::CLASS_LUXURY => 'success',
            self::CLASS_SEMI_LUXURY => 'warning',
            self::CLASS_MIDDLE_CLASS => 'info',
            self::CLASS_EXPRESS => 'danger',
            self::CLASS_ECONOMY => 'secondary',
        ];
        return $colors[$this->class] ?? 'secondary';
    }

    public function getSeatingLayout()
    {
        // --- SEAT MAP LAYOUTS ---
        // Adjust these layouts to match your real bus seat maps.
        // You can add more cases for each real bus type/configuration.
        switch ($this->seating_config) {
            case self::SEATING_1X2:
                // Luxury: 1 seat (left), aisle, 2 seats (right), driver at front left, toilet at back right
                $seatsPerRow = 3;
                $rows = ceil($this->seat_count / $seatsPerRow);
                return [
                    'rows' => $rows,
                    'cols' => 4, // 1 seat + aisle + 2 seats
                    'aisle_positions' => [1],
                    'pattern' => [1, 'aisle', 2],
                    'seats_per_row' => $seatsPerRow,
                    'driver_col' => 0,
                    'toilet_col' => 3,
                ];
            case self::SEATING_2X2:
                // Semi-luxury: 2 seats (left), aisle, 2 seats (right), driver at front left
                $seatsPerRow = 4;
                $rows = ceil($this->seat_count / $seatsPerRow);
                return [
                    'rows' => $rows,
                    'cols' => 5, // 2 seats + aisle + 2 seats
                    'aisle_positions' => [2],
                    'pattern' => [2, 'aisle', 2],
                    'seats_per_row' => $seatsPerRow,
                    'driver_col' => 0,
                ];
            case self::SEATING_2X3:
                // Middle class: 2 seats (left), aisle, 3 seats (right), driver at front left
                $seatsPerRow = 5;
                $rows = ceil($this->seat_count / $seatsPerRow);
                return [
                    'rows' => $rows,
                    'cols' => 6, // 2 seats + aisle + 3 seats
                    'aisle_positions' => [2],
                    'pattern' => [2, 'aisle', 3],
                    'seats_per_row' => $seatsPerRow,
                    'driver_col' => 0,
                ];
            default:
                // fallback to 2x2
                $seatsPerRow = 4;
                $rows = ceil($this->seat_count / $seatsPerRow);
                return [
                    'rows' => $rows,
                    'cols' => 5,
                    'aisle_positions' => [2],
                    'pattern' => [2, 'aisle', 2],
                    'seats_per_row' => $seatsPerRow,
                ];
        }
    }

    /**
     * Generate seats for a bus based on its type and layout (static, reusable)
     * Luxury: 35 seats (1x2), with a double-wide toilet (2 seats) at the end of the double-seat side in the last row.
     * Semi-Luxury: 46 seats (2x2), Middle Class: 60 seats (2x3)
     */
    public static function generateSeatsForBus($bus)
    {
        $layouts = [
            'Luxury' => ['cols' => [1, 2], 'total' => 35], // 1x2, 35 seats, double-wide toilet at back
            'Semi-Luxury' => ['cols' => [2, 2], 'total' => 46], // 2x2, 46 seats
            'Middle Class' => ['cols' => [2, 3], 'total' => 60], // 2x3, 60 seats
        ];
        $type = $bus->type;
        if (!isset($layouts[$type])) return;
        $layout = $layouts[$type];
        $left = $layout['cols'][0];
        $right = $layout['cols'][1];
        $totalPassengerSeats = $layout['total'];
        \common\models\Seat::deleteAll(['bus_id' => $bus->id]);
        $seatNumber = 1;
        $totalSeats = 0;
        if ($type === 'Luxury') {
            // Add driver seat at the very top (not part of main grid)
            $driverSeat = new \common\models\Seat();
            $driverSeat->bus_id = $bus->id;
            $driverSeat->seat_number = 'D';
            $driverSeat->status = 'driver';
            $driverSeat->created_at = $driverSeat->updated_at = time();
            $driverSeat->save(false);

            $cols = 3; // 1x2 layout: 1 left, 2 right
            $rows = 12; // A to L
            $currentSeat = 0;
            $total = $layout['total'];
            for ($r = 0; $r < $rows && $currentSeat < $total; $r++) {
                $rowSeats = [];
                $rowLetter = chr(65 + $r); // A, B, C, ... L
                for ($c = 0; $c < $cols && $currentSeat < $total; $c++) {
                    $label = $rowLetter . ($c + 1);
                    $rowSeats[] = ['num' => $label, 'status' => 'available'];
                    $currentSeat++;
                }
                foreach ($rowSeats as $seatInfo) {
                    $seat = new \common\models\Seat();
                    $seat->bus_id = $bus->id;
                    $seat->seat_number = $seatInfo['num'];
                    $seat->status = $seatInfo['status'];
                    $seat->created_at = $seat->updated_at = time();
                    $seat->save(false);
                }
            }
            $bus->seat_count = $layout['total'];
            $bus->save(false);
            return;
        }
        // Default for other types
        $cols = $left + $right;
        $rows = ceil($totalPassengerSeats / $cols);
        $currentSeat = 0;
        for ($r = 0; $r < $rows; $r++) {
            $rowSeats = [];
            $rowLetter = chr(65 + $r); // A, B, C, ...
            for ($c = 0; $c < $cols; $c++) {
                if ($r == 0 && $c == $cols - 1) {
                    $rowSeats[] = ['num' => 'D', 'status' => 'driver'];
                    continue;
                }
                if ($currentSeat >= $totalPassengerSeats) continue;
                $label = $rowLetter . ($c + 1);
                $rowSeats[] = ['num' => $label, 'status' => 'available'];
                $currentSeat++;
            }
            foreach ($rowSeats as $seatInfo) {
                $seat = new \common\models\Seat();
                $seat->bus_id = $bus->id;
                $seat->seat_number = $seatInfo['num'];
                $seat->status = $seatInfo['status'];
                $seat->created_at = $seat->updated_at = time();
                $seat->save(false);
            }
        }
        $bus->seat_count = $layout['total'];
        $bus->save(false);
    }
} 