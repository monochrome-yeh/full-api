<?php
\Yii::$container->set('yii\grid\ActionColumn', [
    'buttonOptions' => ['class' => 'btn btn-primary'],
]);

return [
    'vendorPath' =>  dirname(dirname(dirname(__DIR__))) . '/vendor',
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
        'mongodb' => [
            'class' => 'yii\mongodb\Connection',
            'dsn' => 'mongodb://localhost:27017/cv',
        ],
        'authManager'=> [
            'class' => 'common\components\monochrome\rbac\MongoDbManager',
            //'defaultRoles' => ['end-user'],
        ],
        'i18n' => [
            'translations' => [
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
                'api*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@api/messages',
                    'fileMap' => [
                        'api/app' => 'app.php',
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
    ],
    'modules' => [
        'members' => [
            'class' => 'common\modules\monochrome\members\Members',
            'systemAdminAllowedIPs' => ['127.0.0.1', '::1', '192.168.*'],
            'login_type' => ['vendor-user','default'],
            'login_fail' => 3,
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
            'custom_role' => [],
            'vendor_worker_role' => [],
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
    ]
];
