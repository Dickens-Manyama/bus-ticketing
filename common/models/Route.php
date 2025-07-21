<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "route".
 *
 * @property int $id
 * @property string $name
 * @property string $origin
 * @property string $destination
 * @property float $price
 * @property int $distance
 * @property int $duration
 * @property int $created_at
 * @property int $updated_at
 * @property string $status
 * @property int $started_at
 * @property int $finished_at
 *
 * @property Booking[] $bookings
 */
class Route extends ActiveRecord
{
    public static function tableName()
    {
        return 'route';
    }

    public function rules()
    {
        return [
            [['name', 'origin', 'destination', 'price', 'created_at', 'updated_at'], 'required'],
            [['price'], 'number'],
            [['distance', 'duration', 'created_at', 'updated_at', 'started_at', 'finished_at'], 'integer'],
            [['name', 'origin', 'destination'], 'string', 'max' => 64],
            [['status'], 'string', 'max' => 20],
            [['status'], 'in', 'range' => ['pending', 'in_progress', 'completed', 'cancelled']],
            [['distance', 'duration'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 'pending'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'origin' => 'Origin',
            'destination' => 'Destination',
            'price' => 'Price (TZS)',
            'distance' => 'Distance (km)',
            'duration' => 'Duration (hours)',
            'status' => 'Status',
            'started_at' => 'Started At',
            'finished_at' => 'Finished At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['route_id' => 'id']);
    }

    public function getFormattedDuration()
    {
        if ($this->duration < 1) {
            return 'Less than 1 hour';
        } elseif ($this->duration == 1) {
            return '1 hour';
        } else {
            return $this->duration . ' hours';
        }
    }

    public function getFormattedDistance()
    {
        return $this->distance . ' km';
    }

    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        return $labels[$this->status] ?? 'Unknown';
    }

    public function getStatusBadgeClass()
    {
        $classes = [
            'pending' => 'bg-secondary',
            'in_progress' => 'bg-warning',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
        ];
        return $classes[$this->status] ?? 'bg-secondary';
    }

    public function canStart()
    {
        return $this->status === 'pending';
    }

    public function canFinish()
    {
        return $this->status === 'in_progress';
    }

    public function isActive()
    {
        return $this->status === 'in_progress';
    }

    public function getFormattedStartedAt()
    {
        return $this->started_at ? date('Y-m-d H:i', $this->started_at) : 'Not started';
    }

    public function getFormattedFinishedAt()
    {
        return $this->finished_at ? date('Y-m-d H:i', $this->finished_at) : 'Not finished';
    }
} 