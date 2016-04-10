<?php

namespace common\modules\monochrome\members\modules\vendorLimit;

use Yii;
use yii\web\ForbiddenHttpException;

class VendorLimit extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\monochrome\members\modules\moduleManager\controllers';

    public $ignore = [];

    private static $vendor;

    private $_ignore = [
        'gii',
        'members',
        'debug',
        'modelValidator',
        'rbam',
        'gridview',
        'moduleManager',
        'userReport',
        'announcement',
        'taxonomy',
        'trace',
    ];

    public function init()
    {
        parent::init();
        // $this->getEnableModules();
        // custom initialization code goes here
    }

    private static function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/monochrome/members/modules/moduleManager/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'zh-TW',
            'basePath' => '@common/modules/monochrome/members/modules/moduleManager/messages',
            'fileMap' => [
                'modules/monochrome/members/modules/moduleManager/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return Yii::t('modules/monochrome/members/modules/moduleManager/' . $category, $message, $params, $language);
    }

    private function getIgnore()
    {
    	if (!is_array($this->ignore)) {
    		$this->ignore = [];
    	}

    	return array_merge($this->_ignore, $this->ignore);
    }

    public function getEnableModules($object = false)
    {
    	$modules = yii\helpers\ArrayHelper::merge(
            require(__DIR__ . '/../../../../../../common/config/main.php'),
            require(__DIR__ . '/../../../../../../common/config/main-local.php'),
            require(__DIR__ . '/../../../../../../frontend/config/main.php'),
            require(__DIR__ . '/../../../../../../frontend/config/main-local.php'),
            require(__DIR__ . '/../../../../../config/main.php'),
            require(__DIR__ . '/../../../../../config/main-local.php')
        )['modules'];
        $array = [];
        foreach ($this->getIgnore() as $value) {
            unset($modules[$value]);
        }

        if ($object === true) {
            foreach (array_keys($modules) as $value) {
                $array[$value] = Yii::$app->getModule($value);
            }
        } else {
            foreach (array_keys($modules) as $value) {
                $array[$value] = self::t('app', $value);
            }
        }

    	return $array;
    }

    public static function checkVendorAccess($module_id)
    {
        $vid = null;
        if (!Yii::$app->user->isGuest && isset(Yii::$app->user->getIdentity()->login['vendor']['vid'])) {
            $vid = Yii::$app->user->getIdentity()->login['vendor']['vid'];
        }

        if ($vid !== null) {
            $model = Yii::$app->mongodb->getCollection('vendor');

            if (
                $model->find([
                    '_id' => $vid,
                    'module' => $module_id,
                ])->count() == 0
            ) {
                throw new ForbiddenHttpException(Yii::t('common/app', 'You are not allowed to access this module.'), 403);
            }
        }
    }

    public static function checkVendorAccessForDisplay($module_id)
    {
        $vid = null;
        $result = true;

        if (!Yii::$app->user->isGuest && isset(Yii::$app->user->getIdentity()->login['vendor']['vid'])) {
            $vid = Yii::$app->user->getIdentity()->login['vendor']['vid'];
        }

        if ($vid !== null) {
            $model = Yii::$app->mongodb->getCollection('vendor');

            if (
                $model->find([
                    '_id' => $vid,
                    'module' => $module_id,
                ])->count() == 0
            ) {
                $result = false;
            }
        } else {
            $result = false;
        }

        return $result;
    }

    public static function is_expire()
    {
        $vid = null;
        if (!Yii::$app->user->isGuest && isset(Yii::$app->user->getIdentity()->login['vendor']['vid'])) {
            $vid = Yii::$app->user->getIdentity()->login['vendor']['vid'];
        }

        if ($vid !== null) {
            $model = Yii::$app->mongodb->getCollection('vendor');

            if (
                $model->find([
                    '_id' => $vid,
                    'expire_date' => ['$lt' => time()],

                ])->count() > 0
            ) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('danger', Yii::t('common/app', '公司使用期限已到，請洽詢樂誌科技 (02)8770-5011'));
            }
        }
    }

    public static function is_active()
    {
        $vid = null;
        if (!Yii::$app->user->isGuest && isset(Yii::$app->user->getIdentity()->login['vendor']['vid'])) {
            $vid = Yii::$app->user->getIdentity()->login['vendor']['vid'];
        }

        if ($vid !== null) {
            $model = Yii::$app->mongodb->getCollection('vendor');

            if (
                $model->find([
                    '_id' => $vid,
                    'status' => 0,

                ])->count() > 0
            ) {
                Yii::$app->user->logout();
            }
        }
    }
}
