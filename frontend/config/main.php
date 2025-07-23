
<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'language' => isset(Yii::$app) && Yii::$app->session->has('language') ? Yii::$app->session->get('language') : (isset($_GET['lang']) ? $_GET['lang'] : 'en'),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\\controllers',
    'defaultRoute' => 'site/index',
    'homeUrl' => '/site/index',
    'components' => [
        'response' => [
            'on beforeSend' => function ($event) {
                $event->sender->headers->add('Content-Security-Policy', "default-src 'self'; img-src 'self' data:;");
            },
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
        ],
        'user' => [
            'identityClass' => 'common\\models\\User',
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
            'hostInfo' => 'http://10.10.4.9:8080',
            'rules' => [
                '' => 'site/index',
                'home' => 'site/index',
                'login' => 'site/login',
                'signup' => 'site/signup',
                'logout' => 'site/logout',
                'about' => 'site/about',
                'contact' => 'site/contact',
                'test' => 'site/test',
                'profile' => 'profile/index',
                'profile/index' => 'profile/index',
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
                'booking/mobile-receipt' => 'booking/mobile-receipt',
                'booking/pdf-receipt' => 'booking/pdf-receipt',
                'booking/my-bookings' => 'booking/my-bookings',
                'booking/cancel-booking' => 'booking/cancel-booking',
                'booking/statistics' => 'booking/statistics',
                'booking/export' => 'booking/export',
                'booking/notifications' => 'booking/notifications',
                'booking/create-test-booking' => 'booking/create-test-booking',
                'booking/verify-ticket' => 'booking/verify-ticket',
                'booking/mobile-verify' => 'booking/mobile-verify',
                'booking/api-verify-ticket' => 'booking/api-verify-ticket',
                'request-password-reset' => 'site/request-password-reset',
                'reset-password' => 'site/reset-password',
                'switch-language' => 'site/switch-language',
                'parcel' => 'parcel/index',
                'parcel/create' => 'parcel/create',
                'parcel/view/<id:\\d+>' => 'parcel/view',
                'parcel/payment/<id:\\d+>' => 'parcel/payment',
                'parcel/pay/<id:\\d+>/<payment_method:\\w+>' => 'parcel/pay',
                'parcel/track' => 'parcel/track',
                'parcel/my-parcels' => 'parcel/my-parcels',
                'parcel/receipt/<id:\\d+>' => 'parcel/receipt',
                'parcel/mobile-verify/<id:\\d+>' => 'parcel/mobile-verify',
            ],
        ],
        'assetManager' => [
            'linkAssets' => false,
        ],
    ],
    'params' => $params,
];
