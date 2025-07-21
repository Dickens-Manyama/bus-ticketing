<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'name' => 'Dickens-OnlineTicketing',
    'basePath' => dirname(__DIR__),
    'language' => isset(Yii::$app) && Yii::$app->session->has('language') ? Yii::$app->session->get('language') : (isset($_GET['lang']) ? $_GET['lang'] : 'en'),
    'controllerNamespace' => 'backend\\controllers',
    'defaultRoute' => 'dashboard/index',
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
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'rbac' => [
            'class' => 'common\components\RbacManager',
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
                '' => 'dashboard/index',
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
                'bus/export-csv' => 'bus/export-csv',
                'route/export-csv' => 'route/export-csv',
                'booking/export-csv' => 'booking/export-csv',
                'user/export-csv' => 'user/export-csv',
            ],
        ],
    ],
    'params' => $params,
];
