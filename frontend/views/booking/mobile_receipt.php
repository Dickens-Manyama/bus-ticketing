<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $booking common\models\Booking */
/* @var $qrImageData string */
/* @var $print boolean */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Receipt - <?= $booking->id ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }
        
        .receipt-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.3;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }
        
        .receipt-title {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .receipt-id {
            font-size: 14px;
            opacity: 0.8;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 30px 20px;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 500;
            color: #666;
            font-size: 14px;
        }
        
        .detail-value {
            font-weight: bold;
            color: #333;
            text-align: right;
            font-size: 14px;
        }
        
        .price {
            font-size: 20px;
            color: #28a745;
        }
        
        .qr-section {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            margin: 20px 0;
        }
        
        .qr-code {
            background: white;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin: 15px 0;
        }
        
        .qr-code img {
            max-width: 120px;
            height: auto;
        }
        
        .status-badge {
            background: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        
        .important-notes {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .important-notes h4 {
            color: #856404;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .important-notes ul {
            list-style: none;
            padding: 0;
        }
        
        .important-notes li {
            color: #856404;
            font-size: 12px;
            margin-bottom: 5px;
            padding-left: 15px;
            position: relative;
        }
        
        .important-notes li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #856404;
        }
        
        .contact-info {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        
        .contact-info p {
            margin: 5px 0;
            font-size: 12px;
            color: #1976d2;
        }
        
        .print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .print-button:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .receipt-container {
                box-shadow: none;
                border: 2px solid #000;
                max-width: none;
                margin: 0;
            }
            
            .print-button {
                display: none !important;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .receipt-container {
                border-radius: 15px;
            }
            
            .header {
                padding: 20px 15px;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .company-name {
                font-size: 20px;
            }
            
            .print-button {
                bottom: 15px;
                right: 15px;
                padding: 10px 16px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <div class="company-name">Dickens-OnlineTicketing</div>
            <div class="receipt-title">✓ Payment Successful - Ticket Receipt</div>
            <div class="receipt-id">Receipt #<?= $booking->id ?> | <?= date('M j, Y H:i', $booking->created_at) ?></div>
        </div>
        
        <div class="content">
            <!-- Booking Information -->
            <div class="section">
                <div class="section-title">
                    <span>🎫</span> Ticket Information
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bus Type:</span>
                    <span class="detail-value"><?= Html::encode($booking->bus->type) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Plate Number:</span>
                    <span class="detail-value"><?= Html::encode($booking->bus->plate_number) ?></span>
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
            
            <!-- Payment Information -->
            <div class="section">
                <div class="section-title">
                    <span>💳</span> Payment Details
                </div>
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
            
            <!-- QR Code -->
            <div class="qr-section">
                <div class="section-title">
                    <span>📱</span> Boarding QR Code
                </div>
                <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Scan this QR code when boarding the bus</p>
                <div class="qr-code">
                    <?php if (isset($qrImageData)): ?>
                        <img src="data:image/png;base64,<?= $qrImageData ?>" alt="QR Code">
                    <?php else: ?>
                        <p style="color: #dc3545; font-weight: bold;">QR Code could not be generated</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Important Notes -->
            <div class="important-notes">
                <h4>⚠️ Important Notes:</h4>
                <ul>
                    <li>Please arrive at least 30 minutes before departure</li>
                    <li>Bring this receipt or show the QR code when boarding</li>
                    <li>Valid ID may be required for verification</li>
                    <li>No refunds for missed departures</li>
                </ul>
            </div>
            
            <!-- Contact Information -->
            <div class="contact-info">
                <p><strong>Thank you for choosing Dickens-OnlineTicketing!</strong></p>
                <p>📧 dickensmanyama8@gmail.com</p>
                <p>📞 +255679165468</p>
            </div>
        </div>
        
        <div class="footer">
            <p>This is your official ticket receipt</p>
            <p>Generated on <?= date('F j, Y \a\t g:i A', $booking->created_at) ?></p>
        </div>
    </div>
    
    <!-- Print Button -->
    <button class="print-button" onclick="window.print()">
        🖨️ Print Receipt
    </button>
    
    <script>
        // Auto-print functionality if print parameter is set
        <?php if ($print): ?>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
        <?php endif; ?>
        
        // Add print functionality to print button
        document.addEventListener('DOMContentLoaded', function() {
            // Hide print button when printing
            const mediaQuery = window.matchMedia('print');
            mediaQuery.addListener(function(mql) {
                const printBtn = document.querySelector('.print-button');
                if (mql.matches) {
                    printBtn.style.display = 'none';
                } else {
                    printBtn.style.display = 'flex';
                }
            });
        });
    </script>
</body>
</html> 