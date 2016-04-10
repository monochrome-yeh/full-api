<?php

namespace common\modules\monochrome\members\components;

use Yii;
use common\modules\monochrome\members\models\Vendor;

class ThemeManager extends \yii\base\Component
{
    public $themes = ['default', 'inspinia'];

    public function getThemes()
    {
        return array_combine($this->themes, $this->themes);
    }

    public function initTheme()
    {
        // $themeName = 'default';

        // if (isset(Yii::$app->user->getIdentity()->settings) && isset(Yii::$app->user->getIdentity()->settings['ui']['theme'])) {
        //     $themeName = Yii::$app->user->getIdentity()->settings['ui']['theme'];
        // } else {
        //     if (isset(Yii::$app->user->getIdentity()->vid)) {
        //         $themeName = Yii::$app->user->getVendor()->settings['ui']['theme'];
        //     }

        //     $sp = explode('/', Yii::$app->request->url);
        //     if (count($sp) >= 3 && in_array('/'.$sp[1], Yii::$app->user->loginUrl) && \MongoId::isValid($sp[2])) {
        //         $vendor = Vendor::find()->where(['_id' => $sp[2]])->asArray()->select(['settings'])->one();
        //         if ($vendor != null) {

        //             //$themeName = $vendor['settings']['ui']['theme'];
        //             //暫時強迫所有vendor都是用 inspinia
        //             $themeName = 'inspinia';
        //         }
        //     }
        // }

        // if (in_array($themeName, $this->themes)) {
        //     static::setTheme($themeName);
        // }
    }

    public static function setTheme($themeName = 'default')
    {
        if ($themeName === 'inspinia') {
            Yii::$app->setComponents([
                'assetManager' => [
                    'class' => 'yii\web\AssetManager',
                    'bundles' => require(YII_ENV_PROD ?  dirname( dirname( dirname( dirname(__DIR__)))).'/config/assets-prod.php' :  dirname( dirname( dirname( dirname(__DIR__)))).'/config/assets-dev.php'),
                    'linkAssets' => true,
                ]
            ]);            
        }

        Yii::$app->setComponents([
            'view' => [
                'class' => 'backend\modules\monochrome\rbam\components\View',
                'theme' => [
                    "pathMap" => [
                        "@frontend/views" => "@frontend/themes/{$themeName}",
                        "@frontend/modules" => "@frontend/themes/{$themeName}/modules",
                        "@common/modules" => "@frontend/themes/{$themeName}/modules"
                    ],
                    "baseUrl" => "@web/themes/{$themeName}",
                ],
            ]
        ]);
    }
}
