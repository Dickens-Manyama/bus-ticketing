<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "seat".
 *
 * @property int $id
 * @property int $bus_id
 * @property string $seat_number
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Bus $bus
 * @property Booking[] $bookings
 */
class Seat extends ActiveRecord
{
    public static function tableName()
    {
        return 'seat';
    }

    public function rules()
    {
        return [
            [['bus_id', 'seat_number', 'status', 'created_at', 'updated_at'], 'required'],
            [['bus_id', 'created_at', 'updated_at'], 'integer'],
            [['seat_number'], 'string', 'max' => 8],
            [['status'], 'string', 'max' => 16],
        ];
    }

    public function getBus()
    {
        return $this->hasOne(Bus::class, ['id' => 'bus_id']);
    }

    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['seat_id' => 'id']);
    }
} 