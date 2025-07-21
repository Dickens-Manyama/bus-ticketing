<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Parcel;

$this->title = 'Parcel Verification - ' . $model->tracking_number;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .verification-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .verification-title {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .content {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .section {
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 500;
            color: #666;
        }
        
        .detail-value {
            font-weight: bold;
            color: #333;
        }
        
        .status-badge {
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-badge.pending {
            background: #ffc107;
            color: #333;
        }
        
        .status-badge.in_transit {
            background: #17a2b8;
        }
        
        .status-badge.delivered {
            background: #28a745;
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
        
        .message-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .verify-button {
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .verify-button:hover {
            background: #218838;
        }
        
        .verify-button:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        .recipient-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .recipient-name {
            font-size: 18px;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 5px;
        }
        
        .recipient-phone {
            color: #666;
            font-size: 14px;
        }
        
        .price {
            color: #28a745;
            font-weight: bold;
        }
        
        .tracking-number {
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 14px;
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="header">
            <div class="company-name">Dickens Bus Company</div>
            <div class="verification-title">📦 Parcel Verification System</div>
        </div>
        
        <div class="content">
            <!-- Tracking Information -->
            <div class="section">
                <div class="section-title">
                    <span>📋</span> Tracking Information
                </div>
                <div class="tracking-number"><?= Html::encode($model->tracking_number) ?></div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge <?= $model->status ?>"><?= Html::encode(ucfirst($model->status)) ?></span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Status:</span>
                    <span class="detail-value">
                        <span class="status-badge"><?= Html::encode(ucfirst($model->payment_status)) ?></span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Created:</span>
                    <span class="detail-value"><?= date('M j, Y H:i', $model->created_at) ?></span>
                </div>
            </div>
            
            <!-- Parcel Information -->
            <div class="section">
                <div class="section-title">
                    <span>📦</span> Parcel Details
                </div>
                <div class="detail-row">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value"><?= Html::encode(ucfirst($model->parcel_type)) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Category:</span>
                    <span class="detail-value"><?= Html::encode($model->getParcelCategoryLabels()[$model->parcel_category] ?? 'Unknown') ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Weight:</span>
                    <span class="detail-value"><?= Html::encode($model->weight) ?> kg</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Route:</span>
                    <span class="detail-value"><?= Html::encode($model->route->name ?? 'N/A') ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Price:</span>
                    <span class="detail-value price"><?= number_format($model->price) ?> TZS</span>
                </div>
            </div>
            
            <!-- Recipient Information -->
            <div class="section">
                <div class="section-title">
                    <span>👤</span> Recipient Information
                </div>
                <div class="recipient-info">
                    <div class="recipient-name"><?= Html::encode($model->recipient_name) ?></div>
                    <div class="recipient-phone">📞 <?= Html::encode($model->recipient_phone) ?></div>
                    <?php if ($model->recipient_address): ?>
                    <div class="recipient-phone">📍 <?= Html::encode($model->recipient_address) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sender Information -->
            <div class="section">
                <div class="section-title">
                    <span>📤</span> Sender Information
                </div>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value"><?= Html::encode($model->sender_name) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value"><?= Html::encode($model->sender_phone) ?></span>
                </div>
            </div>
            
            <!-- Verification Actions -->
            <?php if ($model->status === Parcel::STATUS_IN_TRANSIT || $model->status === Parcel::STATUS_CONFIRMED): ?>
                <div class="message message-warning">
                    ⚠️ Verify recipient's ID before releasing this parcel
                </div>
                <form method="post">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>" />
                    <input type="hidden" name="action" value="verify" />
                    <button type="submit" class="verify-button" onclick="return confirm('Are you sure you want to release this parcel to the recipient?')">
                        ✅ VERIFY & RELEASE PARCEL
                    </button>
                </form>
            <?php elseif ($model->status === Parcel::STATUS_DELIVERED): ?>
                <div class="message message-success">
                    ✅ This parcel has been delivered and released to the recipient
                </div>
                <div class="detail-row">
                    <span class="detail-label">Delivered At:</span>
                    <span class="detail-value"><?= date('M j, Y H:i', $model->updated_at) ?></span>
                </div>
            <?php else: ?>
                <div class="message message-warning">
                    ⏳ This parcel is not ready for delivery yet
                </div>
            <?php endif; ?>
            
            <!-- Instructions -->
            <div class="section">
                <div class="section-title">
                    <span>📋</span> Verification Instructions
                </div>
                <ul style="margin: 0; padding-left: 20px; color: #666;">
                    <li>Ask recipient to present valid ID</li>
                    <li>Verify recipient's name matches the parcel</li>
                    <li>Confirm recipient's phone number</li>
                    <li>Check parcel condition before release</li>
                    <li>Click "Verify & Release" only after confirmation</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
    function verifyParcel() {
        if (confirm('Are you sure you want to release this parcel to the recipient?')) {
            // Submit the form
            document.querySelector('form').submit();
        }
    }
    </script>
</body>
</html> 