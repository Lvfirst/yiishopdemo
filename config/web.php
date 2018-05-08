<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');
$adminmenu=require(__DIR__.'/adminmenu.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute'=>'index',
    'language'=>'zh-cn',
    'charset'=>'utf-8',
    'aliases'=>[
        "@doctorjason/mailerqueue"=>"@vendor/doctorjason/mailerqueue/src"
    ],
    'components' => [

        'asyncLog'=>[
            'class'=>'\\app\\models\\Kafka',
            'broker_list'=>'192.168.137.129:9092',
            'topic'=>'asynclog',
        ],       
        'session'=>[
            'class'=>'yii\redis\Session',
            'redis' => [
               'hostname' => '127.0.0.1',
               'port' => 6379,
               'database' => 4,
            ],
            'keyPrefix'=>'iflash',
        ],

        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'nodes' => [
                ['http_address' => '192.168.137.236:9200'],
             // configure more hosts if you have a cluster
            ],
        ],
        'authManager'=>[
            'class'=>'yii\rbac\DbManager',
            // 指定操作的表
            'itemTable'=>'{{%auth_item}}',//(role permission)
            'itemChildTable'=>'{{%auth_item_child}}',//(role->permission)
            'assignmentTable'=>'{{%auth_assignment}}',
            'ruleTable'=>'{{%auth_rule}}',
            'defaultRoles'=>['guest'], //设置用户默认是什么角色
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'JBxgqLWXxroC5TRx5wJiRH1JQM_IkW7X',
        ],
        'cache' => [
            // 'class' => 'yii\caching\FileCache',
            'class' => 'yii\redis\Cache',
            'redis'=>[
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => '2',
            ],
        ],
        
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'idParam'=>'__user',  //
            'identityCookie'=>['name'=>'__user_identity','httpOnly'=>true],//自定义cookie的相关操作
            'loginUrl'=>['/member/auth'],
        ],
        'admin'=>[
            'class'=>'yii\web\User',
            'identityClass'=>'app\modules\models\Admin',
            'enableAutoLogin'=>true,
            'idParam'=>'__admin',
            'identityCookie'=>['name'=>'__admin_identity','httpOnly'=>true], //renewIdentityCookie
            'loginUrl'=>['/admin/public/login'],
        ],
        'errorHandler' => [  //自定义error页面
            'errorAction' => 'site/error',
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
        // auth
        'authClientCollection'=>[
            'class' => 'yii\authclient\Collection',
            'clients'=>[
                'qq' => [
                    'class'=>'yii\authclient\clients\QqOAuth',
                    'clientId'=>'101443149',
                    'clientSecret'=>'99188198c2ac33a51702a0a3cdf192ce',
                ],               
            ],
        ],        
        //Sentry
        'sentry' => [
                'class' => 'mito\sentry\Component',
                'dsn' => 'https://8de97fe915204c53b36d81a564b0481e:c1f9e949794a491e8a7e749a8be32fef@sentry.io/1200839', // private DSN
                'publicDsn'=>'https://8de97fe915204c53b36d81a564b0481e@sentry.io/1200839',
                'environment' => 'development', // if not set, the default is `production`
                'jsNotifier' => true, // to collect JS errors. Default value is `false`
                'jsOptions' => [ // raven-js config parameter
                    'whitelistUrls' => [ // collect JS errors from these urls  设定收集哪些链接的错误信息
                    // 'http://staging.my-product.com',
                    // 'https://my-product.com',
                ],
            ],
        ],
        'log' => [
                'traceLevel' => YII_DEBUG ? 3 : 0, //设定日志的追踪信息
                'targets' => [
                    [
                        'class' => 'yii\log\FileTarget',
                        'levels' => ['error', 'warning'],
                        'logFile'=>'@app/runtime/logs/application.log',//指定日志存储的地方
                    ],
                    [
                        'class'=> 'yii\log\FileTarget',
                        'levels'=> ['trace','info'],
                        'logFile'=>'@app/runtime/logs/info.log', //存放日志的位置
                        'categories'=>['myinfo'], //指定记录哪个类型，默认记录所有
                        'logVars'=>['_COOKIE'],//记录哪些预定义的常量,比如$_SERVER $_COOKIE
                    ],
                    // 配置Sentry target
                     [
                        'class' => 'mito\sentry\Target',
                        'levels' => ['error', 'warning'],
                        'except' => [
                            'yii\web\HttpException:404',
                        ],
                     ],
                /*    [
                       'class' => 'yii\log\EmailTarget',
                       'mailer' => 'mailer',
                       'levels' => ['error', 'warning'],
                       'message' => [
                        'from' => ['1655585137@qq.com'],
                           'to' => ['534148647@qq.com'],
                           'subject' => '日志处理',
                       ],
                   ],*/
               ],
           ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false, //是否显示index.php
            'suffix'=>'.html', //后缀
            'rules' => [
                '<controller:(index|cart|order)>'=>'<controller>/index',
                'auth'=>'member/auth',
                'product-category-<cateid:\d+>'=>'product/index',
                'product-<productid:\d+>'=>'product/detail',
                'order-check-<orderid:\d+>' => 'order/check',
                [
                    'pattern'=>'myback',
                    'route'=>'/admin/default/index',
                    'suffix'=>'.html',
                ]
                 // '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ],
        ],
        
    ],
    'params' => array_merge($params,['adminmenu'=>$adminmenu]),

];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1','192.168.1.*','192.168.137.*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.179.16']
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    // 开启后台模块
    $config['modules']['admin']=[
        'class'=>'app\modules\admin',
    ];
}

return $config;



