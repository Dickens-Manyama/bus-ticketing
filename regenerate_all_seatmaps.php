<?php
require __DIR__ . '/common/config/bootstrap.php';
require __DIR__ . '/vendor/autoload.php';
Yii::setAlias('@common', __DIR__ . '/common');
Yii::setAlias('@console', __DIR__ . '/console');
Yii::setAlias('@backend', __DIR__ . '/backend');
Yii::setAlias('@frontend', __DIR__ . '/frontend');

use common\models\Bus;
use common\models\Seat;
use backend\controllers\BusController;

// Bootstrap Yii application
$config = require(__DIR__ . '/backend/config/main.php');
$app = new yii\web\Application($config);

$types = ['Luxury', 'Semi-Luxury', 'Middle Class'];
$count = 0;
foreach (Bus::find()->where(['type' => $types])->all() as $bus) {
    // Delete old seats
    Seat::deleteAll(['bus_id' => $bus->id]);
    // Regenerate seats using the controller logic
    $controller = new BusController('bus', $app);
    $controller->generateSeats($bus);
    $count++;
}
echo "Regenerated seats for $count buses.\n"; 