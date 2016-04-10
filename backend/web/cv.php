<?php
//defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'prod');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/cv/bootstrap.php');
require(__DIR__ . '/../config/cv/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/cv/main.php'),
    require(__DIR__ . '/../../common/config/cv/main-local.php'),
    require(__DIR__ . '/../config/cv/main.php'),
    require(__DIR__ . '/../config/cv/main-local.php')
);

$application = new yii\web\Application($config);
$application->run();
