<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/common/config/bootstrap.php';
require_once __DIR__ . '/backend/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/backend/config/main.php'
);

$application = new yii\web\Application($config);

try {
    // Test database connection
    $db = Yii::$app->db;
    echo "Database connection: OK\n";
    
    // Test Parcel model
    $parcel = new \common\models\Parcel();
    echo "Parcel model: OK\n";
    
    // Test price calculation
    $parcel->parcel_type = 'small';
    $parcel->weight = 5;
    $price = $parcel->getCalculatedPrice();
    echo "Price calculation: $price TZS\n";
    
    // Test if parcel table exists
    $tableExists = $db->createCommand("SHOW TABLES LIKE 'parcel'")->queryScalar();
    echo "Parcel table exists: " . ($tableExists ? 'YES' : 'NO') . "\n";
    
    if ($tableExists) {
        // Test table structure
        $columns = $db->createCommand("DESCRIBE parcel")->queryAll();
        echo "Parcel table columns:\n";
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 