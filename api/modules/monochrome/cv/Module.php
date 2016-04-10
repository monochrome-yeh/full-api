<?php

namespace api\modules\monochrome\cv;

use Yii;
class Module extends \yii\base\Module
{

    public $controllerNamespace = 'api\modules\monochrome\cv\controllers';

    public function init()
    {
        parent::init();
    }

    public function beforeAction($action)
    {

        return true;
    }

    private static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/cv/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'zh-TW',
            'basePath' => '@api/modules/monochrome/members/messages',
            'fileMap' => [
                'modules/monochrome/cv/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('modules/monochrome/cv/' . $category, $message, $params, $language);
    }
}
