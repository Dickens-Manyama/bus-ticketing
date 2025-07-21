<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $booking common\models\Booking */
/* @var $qrImageData string */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Receipt - <?= $booking->id ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 18px;
            color: #28a745;
            margin-bottom: 10px;
        }
        .booking-details {
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #007bff;
        }
        .detail-value {
            text-align: right;
        }
        .price {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            border: 2px solid #007bff;
            border-radius: 10px;
        }
        .qr-code {
            margin: 20px 0;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .payment-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .status-badge {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Dickens-OnlineTicketing</div>
        <div class="receipt-title">✓ Payment Successful - Booking Receipt</div>
        <div>Receipt #<?= $booking->id ?> | Date: <?= date('Y-m-d H:i', $booking->created_at) ?></div>
    </div>

    <div class="booking-details">
        <h3>Booking Information</h3>
        <div class="detail-row">
            <span class="detail-label">Bus Type:</span>
            <span class="detail-value"><?= Html::encode($booking->bus->type) ?> (<?= Html::encode($booking->bus->plate_number) ?>)</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Route:</span>
            <span class="detail-value"><?= Html::encode($booking->route->origin) ?> → <?= Html::encode($booking->route->destination) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Seat Number:</span>
            <span class="detail-value"><?= Html::encode($booking->seat->seat_number) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Passenger:</span>
            <span class="detail-value"><?= Html::encode($booking->user->username) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Total Amount:</span>
            <span class="detail-value price"><?= number_format($booking->route->price) ?> TZS</span>
        </div>
    </div>

    <div class="payment-info">
        <h4>Payment Details</h4>
        <div class="detail-row">
            <span class="detail-label">Payment Method:</span>
            <span class="detail-value"><?= Html::encode($booking->payment_method ?? 'M-Pesa') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="detail-value"><span class="status-badge">Completed</span></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Transaction ID:</span>
            <span class="detail-value"><?= Html::encode($booking->qr_code ? json_decode($booking->qr_code, true)['booking_id'] : 'N/A') ?></span>
        </div>
    </div>

    <div class="qr-section">
        <h4>QR Code for Boarding</h4>
        <p>Scan this QR code when boarding the bus</p>
        <div class="qr-code">
            <?php if (isset($qrImageData)): ?>
                <img src="data:image/png;base64,<?= $qrImageData ?>" alt="QR Code" style="max-width: 150px;">
            <?php else: ?>
                <p><strong>QR Code: <?= Html::encode($booking->qr_code) ?></strong></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p><strong>Important Notes:</strong></p>
        <ul style="text-align: left; max-width: 500px; margin: 0 auto;">
            <li>Please arrive at least 30 minutes before departure</li>
            <li>Bring this receipt or show the QR code when boarding</li>
            <li>Valid ID may be required for verification</li>
            <li>No refunds for missed departures</li>
        </ul>
        <br>
        <p>Thank you for choosing Dickens-OnlineTicketing!</p>
        <p>Contact: dickensmanyama8@gmail.com | +255679165468</p>
    </div>
</body>
</html> 