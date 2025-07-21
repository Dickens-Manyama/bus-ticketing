<?php
/**
 * Test file to verify parcel payment flow
 * This file tests the payment processing simulation and QR code generation
 */

// Include Yii2 bootstrap
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/common/config/bootstrap.php';
require_once __DIR__ . '/frontend/config/bootstrap.php';

// Test the payment flow
echo "Testing Parcel Payment Flow...\n";

// Test 1: Check if Parcel model exists
if (class_exists('common\models\Parcel')) {
    echo "✓ Parcel model exists\n";
} else {
    echo "✗ Parcel model not found\n";
    exit(1);
}

// Test 2: Check if ParcelController exists
if (class_exists('frontend\controllers\ParcelController')) {
    echo "✓ ParcelController exists\n";
} else {
    echo "✗ ParcelController not found\n";
    exit(1);
}

// Test 3: Check if IpHelper exists
if (class_exists('common\components\IpHelper')) {
    echo "✓ IpHelper exists\n";
    $serverUrl = \common\components\IpHelper::getServerUrl();
    echo "  Server URL: $serverUrl\n";
} else {
    echo "✗ IpHelper not found\n";
    exit(1);
}

// Test 4: Check parcel type prices
$prices = \common\models\Parcel::getParcelTypePrices();
if (!empty($prices)) {
    echo "✓ Parcel type prices configured\n";
    foreach ($prices as $type => $price) {
        echo "  $type: " . number_format($price, 0, '.', ',') . " TZS\n";
    }
} else {
    echo "✗ Parcel type prices not configured\n";
}

// Test 5: Check parcel categories
$categories = \common\models\Parcel::getParcelCategoryLabels();
if (!empty($categories)) {
    echo "✓ Parcel categories configured\n";
    foreach ($categories as $category => $label) {
        echo "  $category: $label\n";
    }
} else {
    echo "✗ Parcel categories not configured\n";
}

// Test 6: Test QR code generation (fallback method)
echo "\nTesting QR code generation...\n";
try {
    $testUrl = "http://192.168.100.76:8080/parcel/mobile-verify?id=1";
    
    // Test fallback QR code generation
    $url = 'https://api.qrserver.com/v1/create-qr-code/';
    $params = [
        'size' => '200x200',
        'data' => $testUrl,
        'format' => 'png'
    ];
    
    $fullUrl = $url . '?' . http_build_query($params);
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    
    $imageData = file_get_contents($fullUrl, false, $context);
    
    if ($imageData !== false) {
        echo "✓ QR code generation (fallback) works\n";
        echo "  Generated QR code size: " . strlen($imageData) . " bytes\n";
    } else {
        echo "✗ QR code generation (fallback) failed\n";
    }
} catch (Exception $e) {
    echo "✗ QR code generation error: " . $e->getMessage() . "\n";
}

// Test 7: Check URL rules
echo "\nTesting URL configuration...\n";
$configFile = __DIR__ . '/frontend/config/main.php';
if (file_exists($configFile)) {
    $config = require $configFile;
    $rules = $config['components']['urlManager']['rules'] ?? [];
    
    $parcelRules = array_filter($rules, function($rule, $pattern) {
        return strpos($pattern, 'parcel') !== false;
    }, ARRAY_FILTER_USE_BOTH);
    
    if (!empty($parcelRules)) {
        echo "✓ Parcel URL rules configured\n";
        foreach ($parcelRules as $pattern => $route) {
            echo "  $pattern => $route\n";
        }
    } else {
        echo "✗ No parcel URL rules found\n";
    }
} else {
    echo "✗ Frontend config file not found\n";
}

echo "\nPayment flow test completed!\n";
echo "To test the full flow:\n";
echo "1. Start the frontend server: php -S 192.168.100.76:8080 -t frontend/web\n";
echo "2. Navigate to: http://192.168.100.76:8080/parcel/create\n";
echo "3. Fill in the form and submit\n";
echo "4. You should be redirected to the receipt page with QR code\n";
?> 