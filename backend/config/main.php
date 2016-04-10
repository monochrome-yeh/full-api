<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'name' => '行動代銷秘書',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log', 'modelValidator'],
    'components' => [
        'user' => [
            'class' => 'common\modules\monochrome\members\components\User',
            'identityClass' => 'common\modules\monochrome\members\models\AdminUser',
            'loginUrl' => ['/admin-login'],
            'enableAutoLogin' => false,
            'identityCookie' => [
                'name' => '_backendUser', // unique for backend
                'path' => '/backend/web' // correct path for backend app.
            ],
            'idParam' => '__backend',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                '<alias:(logout|security)>' => 'members/default/<alias>',
                '<alias:(profile|admin-login)>' => 'members/user/<alias>',

                '<module:rbam>/<controller:item>/<action:(create|index|update)>/<type:[1-3]>' => '<module>/<controller>/<action>',
                '<module:rbam>/<controller:item>/<action:(update)>/<id:\w+>' => '<module>/<controller>/<action>',
                '<module:members>/<controller:vendor>/<action:(index|create)>' => '<module>/<controller>/<action>',
                '<module:members>/<controller:vendor>/<action:(update|delete)>/<id:\w+>' => '<module>/<controller>/<action>',
                '<module:members>/<controller:vendor>/<action:(login)>/<vid:\w+>' => '<module>/<controller>/<action>',
                '<module:members>/<controller:vendor>/<action:(upload-icon)>/<id:\w+>/<type:[1-4]>' => '<module>/<controller>/<action>',

                '<module:members>/<controller:vendor-user>/<action:(index)>' => '<module>/<controller>/list',
                '<module:members>/<controller:vendor-user>/<action:(update|delete|reset|enable|disable)>/<id:\w+>' => '<module>/<controller>/<action>',

                '<module:trace>/<controller:trace-log>/<action:(index|normal-user|vendor-user|admin-user|create)>' => '<module>/<controller>/<action>',
                '<module:trace>/<controller:trace-log>/<action:(view|update|delete)>/<id:\w+>' => '<module>/<controller>/<action>',

                '<module:top-up>/<controller:vendor>/<action:(index|create)>' => '<module>/<controller>/<action>',
                '<module:top-up>/<controller:vendor>/<action:(view|cancel)>/<id:\w+>' => '<module>/<controller>/<action>',
                '<module:top-up>/<controller:record>/<action:(index)>' => '<module>/<controller>/<action>',

                '<module:members>/<controller:user>/<action:(index|create|profile)>' => '<module>/<controller>/<action>',
                '<module:members>/<controller:user>/<action:(update|delete|reset|enable|disable)>/<id:\w+>' => '<module>/<controller>/<action>',

                //vendor user
                '<module:members>/<controller:vendor-user>/<action:(list)>' => '<module>/<controller>/<action>',

                //project
                '<module:project>/<controller:project-super>/<action:(index|create)>' => '<module>/<controller>/<action>',
                '<module:project>/<controller:project-super>/<action:(update|delete)>/<pid:\w+>' => '<module>/<controller>/<action>',

                //announcement
                '<module:announcement>/<controller:announcement>/<action:(create|index)>' => '<module>/<controller>/<action>',
                '<module:announcement>/<controller:announcement>/<action:(update|view|delete)>/<id:\w+>' => '<module>/<controller>/<action>',

                //userReport
                '/<controller:user-report>/<action:(report)>' => '<module>/<controller>/<action>',
                '<module:userReport>/<controller:user-report>/<action:(index)>' => '<module>/<controller>/<action>',
                '<module:userReport>/<controller:user-report>/<action:(delete)>/<id:\w+>' => '<module>/<controller>/<action>',


                '<module:taxonomy>/<controller:type>/<action:(index|create)>' => '<module>/<controller>/<action>',
                '<module:taxonomy>/<controller:type>/<action:(update|view|delete)>/<id:\w+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\w+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:[0-9a-zA-Z-]+>' => '<module>/<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '/' => 'site/index',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'modules' => [
        'rbam' => [
            'class' => 'backend\modules\monochrome\rbam\RBAM',
            'superadmin_name' => 'superadmin', //default superadmin
            'superadmin_email' => 'n@y.x'
        ],
        'optimize' => [
            'class' => 'backend\modules\monochrome\optimize\Optimize',
        ],
        'trace' => [
            'class' => 'backend\modules\monochrome\trace\Trace',
        ],
        'project' => [
            'class' => 'frontend\modules\project_management\monochrome\project\Project',
        ],
        'top-up' => [
            'class' => 'backend\modules\monochrome\topUp\TopUp',
        ],
    ],
    'params' => $params,
];
