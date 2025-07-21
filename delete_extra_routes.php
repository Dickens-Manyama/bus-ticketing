<?php
// delete_extra_routes.php
require __DIR__ . '/common/config/bootstrap.php';
require __DIR__ . '/vendor/autoload.php';
Yii::setAlias('@common', __DIR__ . '/common');
Yii::setAlias('@console', __DIR__ . '/console');
Yii::setAlias('@backend', __DIR__ . '/backend');
Yii::setAlias('@frontend', __DIR__ . '/frontend');

use common\models\Route;

$routes = Route::find()->orderBy(['id' => SORT_ASC])->all();
if (count($routes) > 10) {
    foreach (array_slice($routes, 10) as $route) {
        $route->delete();
    }
    echo "Deleted routes, only 10 remain.\n";
} else {
    echo "There are already 10 or fewer routes.\n";
} 