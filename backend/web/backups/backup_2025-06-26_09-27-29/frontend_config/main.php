<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'name' => 'Dickens-OnlineTicketing',
    'basePath' => dirname(__DIR__),
    'language' => isset(Yii::$app) && Yii::$app->session->has('language') ? Yii::$app->session->get('language') : (isset($_GET['lang']) ? $_GET['lang'] : 'en'),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'site/index',
    'homeUrl' => '/site/index',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => ['site/login'],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
            'baseUrl' => '',
            'rules' => [
                '' => 'site/index',
                'home' => 'site/index',
                'login' => 'site/login',
                'signup' => 'site/signup',
                'logout' => 'site/logout',
                'about' => 'site/about',
                'contact' => 'site/contact',
                'profile' => 'profile/view',
                'profile/view' => 'profile/view',
                'profile/update' => 'profile/update',
                'booking' => 'booking/bus',
                'booking/bus' => 'booking/bus',
                'booking/route' => 'booking/route',
                'booking/seat' => 'booking/seat',
                'booking/review' => 'booking/review',
                'booking/payment' => 'booking/payment',
                'booking/pay' => 'booking/pay',
                'booking/receipt' => 'booking/receipt',
                'booking/pdf-receipt' => 'booking/pdf-receipt',
                'booking/my-bookings' => 'booking/my-bookings',
                'booking/cancel-booking' => 'booking/cancel-booking',
                'booking/statistics' => 'booking/statistics',
                'booking/export' => 'booking/export',
                'booking/notifications' => 'booking/notifications',
                'request-password-reset' => 'site/request-password-reset',
                'reset-password' => 'site/reset-password',
                'switch-language' => 'site/switch-language',
            ],
        ],
    ],
    'params' => $params,
];
