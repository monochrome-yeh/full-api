<?php

namespace common\modules\monochrome\games;
class Games extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\monochrome\games\controllers';

    public function init()
    {

        parent::init();
        $this->registerTranslations();
        // custom initialization code goes here
    }

    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/games/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'zh-TW',
            'basePath' => '@common/modules/monochrome/games/messages',
            'fileMap' => [
                'modules/monochrome/games/poke' => 'poke.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return \Yii::t('modules/monochrome/games/' . $category, $message, $params, $language);
    }
}
