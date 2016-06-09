<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'cv-api',
    'name' => 'Monochrome-CV',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'modelValidator'],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'class' => 'common\modules\monochrome\members\components\User',
            'identityClass' => 'common\modules\monochrome\members\models\User',
            'loginUrl' => null,
            'enableAutoLogin' => false,
            'identityCookie' => [
                'name' => '_apiUser', // unique for backend
                'path' => '/api/web' // correct path for backend app.
            ],
            'idParam' => '__api',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['cv' => 'cv/api'],
                    'tokens' => [
                        '{id}' => '<uid:\w+>',
                        '{language}' => '<language:(zh_tw|zh_cn|en)>'
                    ],
                    'extraPatterns' => [
                        'GET,HEAD profile/{id}/{language}' => 'profile',
                        // 'GET,HEAD skill-details/{id}' => 'skill-details',
                        // 'GET,HEAD introduction-detail/{id}' => 'introduction-detail',
                        'PUT update/{id}/{language}' => 'update',

                        'OPTIONS update/{id}/{language}' => 'options',
                    ],
                    'pluralize'=> false
                ],
            ],

        ],
    ],
    'modules' => [
        'cv' => [
            'class' => 'api\modules\monochrome\cv\Module',
        ],
    ],
    'params' => $params,
];
