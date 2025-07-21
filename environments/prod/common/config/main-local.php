<?php

return [
    'components' => [
        // 'db' => [
        //     'class' => \yii\db\Connection::class,
        //     'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
        //     'username' => 'root',
        //     'password' => '',
        //     'charset' => 'utf8',
        // ],
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'pgsql:host=localhost;port=5432;dbname=new1',
            'username' => 'postgres',
            'password' => 'root',
            'charset' => 'utf8',
        ],
        'db_pgsql' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'pgsql:host=localhost;port=5432;dbname=new1',
            'username' => 'postgres',
            'password' => 'root',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
        ],
    ],
];
