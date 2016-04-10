<?php

namespace common\modules\monochrome\modelValidator;

use yii\validators\Validator;


class ModelValidator extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\monochrome\modelValidator\controllers';

    public function init()
    {

        parent::init();

        if (!array_key_exists('is_array', Validator::$builtInValidators)) {
            Validator::$builtInValidators['is_array'] = 'common\modules\monochrome\modelValidator\components\IsArrayValidator';

        }

        if (!array_key_exists('is_mongoid', Validator::$builtInValidators)) {
            Validator::$builtInValidators['is_mongoid'] = 'common\modules\monochrome\modelValidator\components\IsMongoIdValidator';

        }

        if (!array_key_exists('embed_doc', Validator::$builtInValidators)) {
            Validator::$builtInValidators['embed_doc'] = 'common\modules\monochrome\modelValidator\components\EmbedDocValidator';

        }
        if (!array_key_exists('array_required', Validator::$builtInValidators)) {
            Validator::$builtInValidators['array_required'] = 'common\modules\monochrome\modelValidator\components\ArrayRequired';

        }        
        // custom initialization code goes here 
    }

    private static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/modelValidator/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'zh-TW',
            'basePath' => '@common/modules/monochrome/modelValidator/messages',
            'fileMap' => [
                'modules/monochrome/modelValidator/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('modules/monochrome/modelValidator/' . $category, $message, $params, $language);
    }
}
