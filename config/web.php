<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$redis = require __DIR__ . '/redis.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'app'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'ApXrOE17br9lwkjD-uuc81FY-sACVLY-',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => 'redis',
            'keyPrefix' => 'PMS_CACHE:'
        ],
        'session' => [
            'class' => 'yii\redis\Session',
            'redis' => 'redis',
            'keyPrefix' => 'PMS_SESSION:',
            'timeout' => 3600
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\Admin',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],
        'errorHandler' => [
            'class' => 'app\components\ErrorHandler',
            'errorAction' => 'rest/error'
        ],
//		'mailer' => [
//			'class' => 'yii\swiftmailer\Mailer',
//			// send all mails to a file by default. You have to set
//			// 'useFileTransport' to false and configure a transport
//			// for the mailer to send real emails.
//			'useFileTransport' => true,
//		],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info', 'trace'],
                ],
            ],
        ],
        'db' => $db,
        'redis' => $redis,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'app' => 'app\components\AppComponent',
        'token' => [
            'class' => 'app\components\TokenComponent',
            'type' => 'cache'
        ],
        'upload' => [
            'class' => 'app\components\UploadComponent',
            'path' => '/home/vagrant/code/pms/web',
            'host' => 'http://pms.test',
            'extensions' => null,
        ],
        'image' => [
            'class' => 'app\components\ImageComponent'
        ]
    ],
    'modules' => [
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['components']['log']['targets'][0]['levels'][] = 'trace';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
