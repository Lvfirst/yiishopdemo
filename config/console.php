<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases'=>[
            "@doctorjason/mailerqueue"=>"@vendor/doctorjason/mailerqueue/src"
    ],    
    'components' => [

        'asyncLog'=>[
            'class'=>'\\app\\models\\Kafka',
            'broker_list'=>'192.168.137.129:9092',
            'topic'=>'asynclog',
        ],

        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],        
        'mailer' => [
            // 'class' => 'yii\swiftmailer\Mailer',
            'class' => 'doctorjason\mailerqueue\MailerQueue',
            'db'=>'1',
            'key'=>'mails',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false, //false 是发送邮件
            'transport'=>[
                'class'=>'Swift_SmtpTransport',
                'host'=>'smtp.qq.com',
                'username'=>'1655585137@qq.com',
                'password'=>'krcassykmxrteich',
                'port'=>'465',
                'encryption'=>'ssl',
            ],
        ],        
       'authManager'=>[
        'class'=>'yii\rbac\DbManager',
            // 指定操作的表
            'itemTable'=>'{{%auth_item}}',//(role permission)
            'itemChildTable'=>'{{%auth_item_child}}',//(role->permission)
            'assignmentTable'=>'{{%auth_assignment}}',
            'ruleTable'=>'{{%auth_rule}}',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
      
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['testkafka'],
                    'logVars' => [],
                    'exportInterval' => 1, //有一条消息就刷到文件里
                    'logFile' => '@app/runtime/logs/Kafka.log',
                ]
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
