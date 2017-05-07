<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'request' => [
            //'enableCookieValidation' => false,
           // 'cookieValidationKey' => '',
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'fA9dGJhY4U5H5b5nMZyiBg9_89nCH61U',
            //подключение парсера на входящие данные в формате json
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true,
            'enableSession' => false,

        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
//            'transport' => [
//                'class' => 'Swift_SmtpTransport',
//                'host' => 'smtp.yandex.ru',
//                'username' => 'login @yandex.ru',
//                'password' => 'fdpass3Gd',
//                'port' => '465',
//                'encryption' => 'ssl', // у яндекса SSL
//            ],
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],

        // Украинский модуль рассылки сообщений смс "TurboSMS" на номера телефона
        // руководство пользования https://github.com/AVATOR/yii2-turbosms
        /*'turbosms' => [
            'class' => 'avator\turbosms\Turbosms',//класс в папке vendor/..
            'sender' => 'AntonPHP',// подпись - название компании https://turbosms.ua/sign.html
            'login' => 'AntonPHP',// логин шлюза https://turbosms.ua/route.html
            'password' => 'Koksohim89',// пароль шлюза https://turbosms.ua/route.html
        ],*/
        //тестовый режим без отправки, только заходит в базу данных
        'turbosms' => [
            'class' => 'avator\turbosms\Turbosms',
            'sender' => 'your_sender',
            'login' => 'koksohim',
            'password' => 'Koksohim89',
            'debug' => true,
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [

                ['class'=>'yii\rest\UrlRule', 'controller'=>'news',
                    'extraPatterns'=>[
                        'GET last_id'=>'last_id',
                        'GET last_limit'=>'last_limit',
                        'GET old_id'=>'old_id',
                        'POST,GET search_id_tags'=>'search_id_tags',
                       // 'OPTIONS news' => 'options',
                    ],
                    'prefix'=>'api/v1',
                ],
                ['class'=>'yii\rest\UrlRule', 'controller'=>'user',
                    'extraPatterns'=>[
                        'POST,GET login'=>'login',
                        'POST,GET token'=>'token',
                        'POST,GET verify'=>'verify',
                        'POST,GET send_form'=>'send',
                        'POST,GET reset_password'=>'reset',
                    ],
                    'prefix'=>'api/v1'
                ],
                ['class'=>'yii\rest\UrlRule', 'controller'=>'question',

                    'prefix'=>'api/v1',
                ],
                ['class'=>'yii\rest\UrlRule', 'controller'=>'message',
                    'extraPatterns'=>[

                    ],
                    'prefix'=>'api/v1',
                ],
                ['class'=>'yii\rest\UrlRule', 'controller'=>'visit',
                    'extraPatterns'=>[

                    ],
                    'prefix'=>'api/v1',
                ],
                ['class'=>'yii\rest\UrlRule', 'controller'=>'bookmark',
                    'extraPatterns'=>[

                    ],
                    'prefix'=>'api/v1',
                ],
                ['class'=>'yii\rest\UrlRule', 'controller'=>'like',
                    'extraPatterns'=>[

                    ],
                    'prefix'=>'api/v1',
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
