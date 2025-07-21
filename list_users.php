<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require_once __DIR__ . '/common/config/bootstrap.php';
require_once __DIR__ . '/backend/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/common/config/main-local.php',
    require __DIR__ . '/backend/config/main.php',
    require __DIR__ . '/backend/config/main-local.php'
);

$application = new yii\web\Application($config);

$users = \common\models\User::find()->all();
foreach ($users as $user) {
    echo "ID: {$user->id}, Username: {$user->username}, Email: {$user->email}, Role: {$user->role}, Status: {$user->status}, Created: {$user->created_at}, Updated: {$user->updated_at}\n";
} 