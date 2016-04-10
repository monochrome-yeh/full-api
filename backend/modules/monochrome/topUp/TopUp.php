<?php

namespace backend\modules\monochrome\topUp;

class TopUp extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\monochrome\topUp\controllers';

    public function init()
    {
        parent::init();
    }

    private static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/topUp/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'zh-TW',
            'basePath' => '@backend/modules/monochrome/topUp/messages',
            'fileMap' => [
                'modules/monochrome/topUp/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('modules/monochrome/topUp/' . $category, $message, $params, $language);
    }
}
