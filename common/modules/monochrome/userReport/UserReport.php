<?php

namespace common\modules\monochrome\userReport;

class UserReport extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\monochrome\userReport\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    private static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/userReport/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'zh-TW',
            'basePath' => '@common/modules/monochrome/userReport/messages',
            'fileMap' => [
                'modules/monochrome/userReport/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('modules/monochrome/userReport/' . $category, $message, $params, $language);
    }      
}
