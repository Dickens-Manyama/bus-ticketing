<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $booking common\models\Booking */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
    <title>Ticket Verification - <?= $booking->id ?></title>
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
        
        .verification-container {
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
        
        .verification-title {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .ticket-id {
            font-size: 14px;
            opacity: 0.8;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 30px 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-used {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-expired {
            background: #fff3cd;
            color: #856404;
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
        
        .verify-button {
            background: #28a745;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .verify-button:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .verify-button:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
        
        .message {
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
        }
        
        .message-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .message-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .verification-container {
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
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="header">
            <div class="company-name">Dickens-OnlineTicketing</div>
            <div class="verification-title">🎫 Ticket Verification</div>
            <div class="ticket-id">Ticket #<?= $booking->id ?> | <?= date('M j, Y H:i', $booking->created_at) ?></div>
        </div>
        
        <div class="content">
            <!-- Status Badge -->
            <div class="text-center">
                <?php if ($booking->isActive()): ?>
                    <span class="status-badge status-active">✅ ACTIVE TICKET</span>
                <?php elseif ($booking->isUsed()): ?>
                    <span class="status-badge status-used">❌ TICKET USED</span>
                <?php elseif ($booking->isExpired()): ?>
                    <span class="status-badge status-expired">⚠️ TICKET EXPIRED</span>
                <?php endif; ?>
            </div>
            
            <!-- Passenger Information -->
            <div class="section">
                <div class="section-title">
                    <span>👤</span> Passenger Details
                </div>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value"><?= Html::encode($booking->user->username) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?= Html::encode($booking->user->email) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">User ID:</span>
                    <span class="detail-value">#<?= $booking->user->id ?></span>
                </div>
            </div>
            
            <!-- Journey Information -->
            <div class="section">
                <div class="section-title">
                    <span>🚌</span> Journey Details
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
                    <span class="detail-label">Price:</span>
                    <span class="detail-value"><?= number_format($booking->route->price) ?> TZS</span>
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
                    <span class="detail-label">Payment Status:</span>
                    <span class="detail-value"><?= Html::encode(ucfirst($booking->payment_status ?? 'completed')) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Booking Status:</span>
                    <span class="detail-value"><?= Html::encode(ucfirst($booking->status)) ?></span>
                </div>
            </div>
            
            <!-- Verification Actions -->
            <?php if ($booking->isActive()): ?>
                <div class="message message-success">
                    ✅ This ticket is valid and ready for boarding
                </div>
                <button class="verify-button" onclick="verifyTicket(<?= $booking->id ?>)">
                    ✅ VERIFY & ALLOW BOARDING
                </button>
            <?php elseif ($booking->isUsed()): ?>
                <div class="message message-error">
                    ❌ This ticket has already been used for boarding
                </div>
                <div class="detail-row">
                    <span class="detail-label">Scanned At:</span>
                    <span class="detail-value"><?= $booking->scanned_at ? date('M j, Y H:i', $booking->scanned_at) : 'N/A' ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Scanned By:</span>
                    <span class="detail-value">
                        <?php if ($booking->scannedBy): ?>
                            <?= Html::encode($booking->scannedBy->username) ?>
                        <?php else: ?>
                            📱 Scanned by Phone (Mobile QR Verification)
                        <?php endif; ?>
                    </span>
                </div>
                <button class="verify-button" disabled>
                    ❌ TICKET ALREADY USED
                </button>
            <?php elseif ($booking->isExpired()): ?>
                <div class="message message-warning">
                    ⚠️ This ticket has expired and cannot be used
                </div>
                <button class="verify-button" disabled>
                    ⚠️ TICKET EXPIRED
                </button>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>Ticket Verification System</p>
            <p>Scanned on <?= date('F j, Y \a\t g:i A') ?></p>
        </div>
    </div>
    
    <script>
        function verifyTicket(bookingId) {
            if (confirm('Are you sure you want to verify this ticket and allow boarding?')) {
                // Show loading state
                const button = document.querySelector('.verify-button');
                button.textContent = '🔄 VERIFYING...';
                button.disabled = true;
                
                // Make API call to verify ticket (CSRF disabled for API endpoint)
                fetch(`/booking/api-verify-ticket?id=${bookingId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'message message-success';
                        messageDiv.innerHTML = '✅ ' + data.message;
                        document.querySelector('.content').insertBefore(messageDiv, document.querySelector('.verify-button'));
                        
                        // Update button
                        button.textContent = '✅ VERIFIED';
                        button.style.background = '#28a745';
                        
                        // Reload page after 2 seconds to show updated status
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        // Show error message
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'message message-error';
                        messageDiv.innerHTML = '❌ ' + data.message;
                        document.querySelector('.content').insertBefore(messageDiv, document.querySelector('.verify-button'));
                        
                        // Reset button
                        button.textContent = '✅ VERIFY & ALLOW BOARDING';
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error message
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message message-error';
                    messageDiv.innerHTML = '❌ Network error. Please try again. Error: ' + error.message;
                    document.querySelector('.content').insertBefore(messageDiv, document.querySelector('.verify-button'));
                    
                    // Reset button
                    button.textContent = '✅ VERIFY & ALLOW BOARDING';
                    button.disabled = false;
                });
            }
        }
    </script>
</body>
</html> 