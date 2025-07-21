<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'language' => isset(Yii::$app) && Yii::$app->session->has('language') ? Yii::$app->session->get('language') : (isset($_GET['lang']) ? $_GET['lang'] : 'en'),
    'controllerNamespace' => 'backend\\controllers',
    'defaultRoute' => 'site/login',
    'homeUrl' => '/dashboard/index',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'baseUrl' => '',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // Use separate session name for backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'baseUrl' => '',
            'rules' => [
                'dashboard' => 'dashboard/index',
                'dashboard/index' => 'dashboard/index',
                'dashboard/test' => 'dashboard/test',
                'dashboard/debug' => 'dashboard/debug',
                'site/login' => 'site/login',
                'site/logout' => 'site/logout',
                'site/test' => 'site/test',
                'site/debug' => 'site/debug',
                'profile' => 'profile/index',
                'profile/index' => 'profile/index',
                'profile/update' => 'profile/update',
                'profile/change-password' => 'profile/change-password',
                'profile/upload-picture' => 'profile/upload-picture',
                'profile/test' => 'profile/test',
                'reports' => 'reports/index',
                'reports/index' => 'reports/index',
                'reports/super-admin' => 'reports/super-admin',
                'reports/admin' => 'reports/admin',
                'reports/bookings' => 'reports/bookings',
                'reports/fleet' => 'reports/fleet',
                'reports/revenue' => 'reports/revenue',
                'reports/route' => 'reports/route',
                'reports/users' => 'reports/users',
                'bus/export-csv' => 'bus/export-csv',
                'route/export-csv' => 'route/export-csv',
                'booking/export-csv' => 'booking/export-csv',
                'user/export-csv' => 'user/export-csv',
                'seat-monitoring' => 'seat-monitoring/index',
                'seat-monitoring/index' => 'seat-monitoring/index',
                'seat-monitoring/bus-seats' => 'seat-monitoring/bus-seats',
                'seat-monitoring/real-time-dashboard' => 'seat-monitoring/real-time-dashboard',
                'seat-monitoring/start-journey' => 'seat-monitoring/start-journey',
                'seat-monitoring/finish-journey' => 'seat-monitoring/finish-journey',
                'seat-monitoring/start-new-journey' => 'seat-monitoring/start-new-journey',
                'seat-monitoring/get-seat-data' => 'seat-monitoring/get-seat-data',
                'parcel/<action:\w+>' => 'parcel/<action>',
                'parcel/<action:\w+>/<id:\d+>' => 'parcel/<action>',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'assetManager' => [
            'linkAssets' => false,
        ],
    ],
    'params' => array_merge($params, [
        'frontendUrl' => 'http://192.168.100.76:8082', // Updated to match frontend server
    ]),
];
