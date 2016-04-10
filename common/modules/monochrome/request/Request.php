<?php

namespace common\modules\monochrome\request;

class Request extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\monochrome\request\controllers';

    public function init()
    {
        parent::init();
        \common\modules\monochrome\members\modules\vendorLimit\VendorLimit::checkVendorAccess($this->id);
    }

    private static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/request/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@common/modules/monochrome/request/messages',
            'fileMap' => [
                'modules/monochrome/request/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('modules/monochrome/request/' . $category, $message, $params, $language);
    }
}
