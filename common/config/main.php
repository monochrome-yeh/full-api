<?php
\Yii::$container->set('yii\grid\ActionColumn', [
    'buttonOptions' => ['class' => 'btn btn-primary'],
]);

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'name' => '行動代銷秘書',
    'timeZone' => 'Asia/Taipei',
    'language' => 'zh-TW',
    'aliases' => [
        '@logazine' => '@vendor/logazine',
    ],
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace', 'error', 'warning'],
                ],
                'mongodb' => [
                    'logCollection' => 'trace_log',
                    'class' => 'yii\mongodb\log\MongoDbTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => ['action_trace'],
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'frontend_mongodb_cache' => [
            'class' => 'yii\mongodb\Cache',
            'db' => 'mongodb',
            'cacheCollection' => 'frontend_cache',
        ],
        'other_cache' => [
            'class' => 'yii\mongodb\Cache',
            'db' => 'mongodb',
            'cacheCollection' => 'other_cache',
        ],
        'view' => [
            'class' => 'backend\modules\monochrome\rbam\components\View',
        ],
        'convert' => [
            'class' => 'common\components\monochrome\convert\Convert',
        ],
        's3' => [
            'class' => 'common\components\monochrome\aws\S3',
            'enable' => false,
            'key' => 'xxxxxxxxxxxxxxxxxxx',
            'secret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'bucket' => 'BUCKET',
            'url' => 'https://s3-ap-*-*.amazonaws.com/*',
            'pre_folder' => '*',
        ],
        'sms' => [
            'class' => 'common\components\monochrome\mitake\Sms',
            'account' => '00000000',
            'password' => 'aaaaaaaa',
            'enable' => false,
        ],
        'zipCodeTW' => [
            'class' => 'common\components\monochrome\zipCodeTW',
        ],
        'authManager'=> [
            'class' => 'common\components\monochrome\rbac\MongoDbManager',
            //'defaultRoles' => ['end-user'],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'google@gmail.com',
                'password' => '#####',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
        'mongodb' => [
            'class' => 'yii\mongodb\Connection',
            'dsn' => 'mongodb://localhost:27017/new_app',
        ],
        'i18n' => [
            'translations' => [
                'frontend*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@frontend/messages',
                    'fileMap' => [
                        'frontend/  app' => 'app.php',
                    ],
                ],
                'backend*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'fileMap' => [
                        'backend/app' => 'app.php',
                    ],
                ],
                'common*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'common/app' => 'app.php',
                        'common/commission_agents' => 'commission_agents.php',
                        'common/menu' => 'menu.php',
                    ],
                ],
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/yiisoft/yii2/messages',
                    'fileMap' => [
                        'app' => 'yii.php',
                    ],
                ],
            ],
        ],
        'formatter' => [
            'class' => 'common\components\monochrome\i18n\Formatter',
            'currencyCode' => '$',
            'thousandSeparator' => ',',
            'currencyDivisor' => 10000,
            'currencySuffix' => ' 萬',
            'currencyDecimal' => 4,
            'timeFormat' => 'php:H:i:s',
            'dateFormat' => 'php:Y-m-d',
            'datetimeFormat' => 'php:Y-m-d H:i:s',
            'dateWeek' => true,
            'booleanFormat' => ['×', '√'],
        ],
        // 'convert' => [
        //     'class' => 'common\components\convert\Convert',
        // ]
        'googleAnalytics' => [
            'class' => 'common\components\monochrome\google\GoogleAnalytics',
        ],
    ],
    'modules' => [
        'members' => [
            'class' => 'common\modules\monochrome\members\Members',
            'systemAdminAllowedIPs' => ['127.0.0.1', '::1', '192.168.*'],
            'login_type' => ['vendor-user','default'],
            'login_fail' => 3,
            'google' => [
                'recaptcha' => [
                    'enable' => false,
                    'secret' => '6Lep5wkTAAAAAN2-aqEMlzQ7QxLl_Oy924QvQ8pV',
                ],
            ],
            'securityUpdateExpair' => 7200,
            'emailPassword' => [
                'enable' => true,
                'template' => [
                    'register' => '@common/modules/monochrome/members/templates/register',
                    'notification' => '@common/modules/monochrome/members/templates/notification',
                    'notification_enable' => '@common/modules/monochrome/members/templates/notification_enable',
                    'notification_disable' => '@common/modules/monochrome/members/templates/notification_disable',
                    'reset_password' => '@common/modules/monochrome/members/templates/reset_password',
                    'vendor_register' => '@common/modules/monochrome/members/templates/vendor_register',
                    'login_fail_too_much' => '@common/modules/monochrome/members/templates/login_fail_too_much',
                    'vendor_user_login_too_much' => '@common/modules/monochrome/members/templates/vendor_user_login_too_much',
                ]
            ],
            'custom_role' => ['boss', 'manager', 'sales', 'owner', 'accountant'],
            'vendor_worker_role' => ['manager', 'sales'],
            'vendor_admin_role' => 'admin',
            'securityVerification' => false, //for update

            'modules' => [
                'vendor_limit' => [
                    'class' => 'common\modules\monochrome\members\modules\vendorLimit\VendorLimit',
                ],
            ],
            'components' => [
                'theme_manager' => [
                    'class' => 'common\modules\monochrome\members\components\ThemeManager'
                ]
            ],
        ],
        'modelValidator' => [
            'class' => 'common\modules\monochrome\modelValidator\ModelValidator',
        ],
        'taxonomy' => [
            'class' => 'common\modules\monochrome\taxonomy\Taxonomy',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to use your own export download action or custom translation message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => [],
        ],
        'announcement' => [
            'class' => 'common\modules\monochrome\announcement\Announcement',
        ],
        'userReport' => [
            'class' => 'common\modules\monochrome\userReport\UserReport',
        ],
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['192.168.*', '127.0.0.1', '54.183.219.156', '::1'],
        ],
        'alert' => [
            'class' => 'common\modules\monochrome\alert\Alert',
        ],
        'request' => [
            'class' => 'common\modules\monochrome\request\Request',
        ],
        // 'modelDecorator' => [
        //     'class' => 'common\modules\monochrome\modelDecorator\ModelDecorator',
        // ],
    ]
];            
