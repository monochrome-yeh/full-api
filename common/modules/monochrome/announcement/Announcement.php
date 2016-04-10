<?php

namespace common\modules\monochrome\announcement;

class Announcement extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\monochrome\announcement\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    private static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/announcement/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'zh-TW',
            'basePath' => '@common/modules/monochrome/announcement/messages',
            'fileMap' => [
                'modules/monochrome/announcement/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('modules/monochrome/announcement/' . $category, $message, $params, $language);
    }    
}
