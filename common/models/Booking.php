<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "booking".
 *
 * @property int $id
 * @property int $user_id
 * @property int $bus_id
 * @property int $seat_id
 * @property int $route_id
 * @property string|null $status
 * @property string|null $payment_info
 * @property string|null $payment_method
 * @property string|null $payment_status
 * @property string|null $receipt
 * @property string|null $qr_code
 * @property int $created_at
 * @property int $updated_at
 * @property int $scanned_at
 * @property int $scanned_by
 * @property string $ticket_status
 *
 * @property User $user
 * @property Bus $bus
 * @property Seat $seat
 * @property Route $route
 */
class Booking extends ActiveRecord
{
    public static function tableName()
    {
        return 'booking';
    }

    public function rules()
    {
        return [
            [['user_id', 'bus_id', 'seat_id', 'route_id', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'bus_id', 'seat_id', 'route_id', 'created_at', 'updated_at', 'scanned_at', 'scanned_by'], 'integer'],
            [['payment_info', 'receipt', 'qr_code'], 'string', 'max' => 255],
            [['status', 'payment_status', 'ticket_status'], 'string', 'max' => 20],
            [['payment_method'], 'string', 'max' => 50],
            [['status'], 'default', 'value' => 'pending'],
            [['payment_status'], 'default', 'value' => 'pending'],
            [['ticket_status'], 'default', 'value' => 'active'],
            [['status'], 'in', 'range' => ['pending', 'confirmed', 'cancelled', 'completed']],
            [['payment_status'], 'in', 'range' => ['pending', 'completed', 'failed', 'refunded']],
            [['ticket_status'], 'in', 'range' => ['active', 'expired', 'used']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'bus_id' => 'Bus ID',
            'seat_id' => 'Seat ID',
            'route_id' => 'Route ID',
            'status' => 'Status',
            'payment_info' => 'Payment Info',
            'payment_method' => 'Payment Method',
            'payment_status' => 'Payment Status',
            'receipt' => 'Receipt',
            'qr_code' => 'QR Code',
            'ticket_status' => 'Ticket Status',
            'scanned_at' => 'Scanned At',
            'scanned_by' => 'Scanned By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getBus()
    {
        return $this->hasOne(Bus::class, ['id' => 'bus_id']);
    }

    public function getSeat()
    {
        return $this->hasOne(Seat::class, ['id' => 'seat_id']);
    }

    public function getRoute()
    {
        return $this->hasOne(Route::class, ['id' => 'route_id']);
    }

    public function getScannedBy()
    {
        return $this->hasOne(User::class, ['id' => 'scanned_by']);
    }

    /**
     * Get status options for dropdown
     */
    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed',
        ];
    }

    /**
     * Get payment status options for dropdown
     */
    public static function getPaymentStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
        ];
    }

    /**
     * Get payment method options for dropdown
     */
    public static function getPaymentMethodOptions()
    {
        return [
            'M-Pesa' => 'M-Pesa',
            'Airtel Money' => 'Airtel Money',
            'Tigo Pesa' => 'Tigo Pesa',
            'Cash' => 'Cash',
            'Card' => 'Card',
        ];
    }

    /**
     * Get ticket status options for dropdown
     */
    public static function getTicketStatusOptions()
    {
        return [
            'active' => 'Active',
            'expired' => 'Expired',
            'used' => 'Used',
        ];
    }

    /**
     * Check if ticket is active
     */
    public function isActive()
    {
        return $this->ticket_status === 'active';
    }

    /**
     * Check if ticket is expired
     */
    public function isExpired()
    {
        return $this->ticket_status === 'expired';
    }

    /**
     * Check if ticket is used
     */
    public function isUsed()
    {
        return $this->ticket_status === 'used';
    }

    /**
     * Mark ticket as used
     */
    public function markAsUsed($scannedByUserId = null)
    {
        $this->ticket_status = 'used';
        $this->scanned_at = time();
        $this->scanned_by = $scannedByUserId;
        $this->updated_at = time();
        return $this->save(false);
    }

    /**
     * Mark ticket as expired
     */
    public function markAsExpired()
    {
        $this->ticket_status = 'expired';
        $this->updated_at = time();
        return $this->save(false);
    }
} 