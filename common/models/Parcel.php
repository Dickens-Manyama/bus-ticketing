<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "parcel".
 */
class Parcel extends ActiveRecord
{
    // Parcel types
    const TYPE_SMALL = 'small';
    const TYPE_MEDIUM = 'medium';
    const TYPE_LARGE = 'large';
    const TYPE_EXTRA_LARGE = 'extra_large';

    // Parcel categories
    const CATEGORY_FOOD = 'food';
    const CATEGORY_LETTERS = 'letters';
    const CATEGORY_ELECTRICAL = 'electrical';
    const CATEGORY_FRAGILE = 'fragile';
    const CATEGORY_OTHER = 'other';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    // Payment status constants
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';

    public static function tableName()
    {
        return 'parcel';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'parcel_type', 'weight', 'route_id', 'price', 'sender_name', 'sender_phone', 'recipient_name', 'recipient_phone'], 'required'],
            [['user_id', 'route_id', 'payment_date', 'departure_date', 'created_at', 'updated_at'], 'integer'],
            [['weight', 'price'], 'number'],
            [['tracking_number', 'parcel_type', 'parcel_category', 'description', 'status', 'payment_status', 'payment_method', 'sender_name', 'sender_phone', 'sender_address', 'recipient_name', 'recipient_phone', 'recipient_address'], 'string', 'max' => 255],
            [['tracking_number'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tracking_number' => 'Tracking Number',
            'user_id' => 'User ID',
            'parcel_type' => 'Parcel Type',
            'weight' => 'Weight (kg)',
            'parcel_category' => 'Parcel Category',
            'description' => 'Description',
            'route_id' => 'Route',
            'price' => 'Price (TZS)',
            'status' => 'Status',
            'payment_status' => 'Payment Status',
            'payment_method' => 'Payment Method',
            'payment_date' => 'Payment Date',
            'sender_name' => 'Sender Name',
            'sender_phone' => 'Sender Phone',
            'sender_address' => 'Sender Address',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => 'Recipient Phone',
            'recipient_address' => 'Recipient Address',
            'departure_date' => 'Departure Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getParcelTypeLabels()
    {
        return [
            self::TYPE_SMALL => 'Small (Up to 5kg)',
            self::TYPE_MEDIUM => 'Medium (5-15kg)',
            self::TYPE_LARGE => 'Large (15-30kg)',
            self::TYPE_EXTRA_LARGE => 'Extra Large (30kg+)',
        ];
    }

    public static function getParcelCategoryLabels()
    {
        return [
            self::CATEGORY_FOOD => 'Food Items',
            self::CATEGORY_LETTERS => 'Letters & Documents',
            self::CATEGORY_ELECTRICAL => 'Electrical Appliances',
            self::CATEGORY_FRAGILE => 'Fragile Items',
            self::CATEGORY_OTHER => 'Other Items',
        ];
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_IN_TRANSIT => 'In Transit',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function getPaymentStatusLabels()
    {
        return [
            self::PAYMENT_STATUS_PENDING => 'Pending',
            self::PAYMENT_STATUS_PAID => 'Paid',
            self::PAYMENT_STATUS_FAILED => 'Failed',
        ];
    }

    /**
     * Calculate price based on type and weight (per kg pricing)
     */
    public function getCalculatedPrice()
    {
        $rates = [
            self::TYPE_SMALL => 1000,      // per kg
            self::TYPE_MEDIUM => 1500,     // per kg
            self::TYPE_LARGE => 2000,      // per kg
            self::TYPE_EXTRA_LARGE => 2500 // per kg
        ];
        $rate = $rates[$this->parcel_type] ?? 1000;
        return round($rate * (float)$this->weight);
    }
} 