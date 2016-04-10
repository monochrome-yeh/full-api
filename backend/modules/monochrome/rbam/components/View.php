<?php

namespace backend\modules\monochrome\rbam\components;

use yii\web\View as BaseView;
use Yii;
use common\modules\monochrome\members\models\Vendor;

class View extends BaseView
{

    public $vendorName;

    public function init()
    {
        parent::init();
        $vid = 0;
        //set default icons
        $icons = [
            's_57x57' => '',
            's_72x72' => '',
            's_114x114' => '',
            's_144x144' => '',
        ];

        if (!Yii::$app->user->isGuest && isset(Yii::$app->user->getIdentity()->vid)) {
            $vendor = Yii::$app->user->getVendor();
            $this->vendorName = $vendor->name;
        } else {
        	$sp = explode('/', Yii::$app->request->url);
            if (count($sp) >= 3 && in_array('/'.$sp[1], (array)Yii::$app->user->loginUrl) && \MongoId::isValid($sp[2])) {

                $vendor = Vendor::find()->where(['_id' => $sp[2]])->asArray()->select(['settings', 'name'])->one();
                if ($vendor != null) {
                    $icons = array_merge($icons, (array)$vendor['settings']['ui']['icons']);
                    $this->vendorName = $vendor['name'];
                }
            }

        }

        //print_r($icons);exit;
        $this->registerLinkTag([
            'rel' => "apple-touch-icon-precomposed",
            'href' => Yii::$app->s3->createUrl($icons['s_57x57']),
        ]);
        $this->registerLinkTag([
            'rel' => "apple-touch-icon-precomposed",
            'href' => Yii::$app->s3->createUrl($icons['s_72x72']),
            'sizes' => "114x114",
        ]);
        $this->registerLinkTag([
            'rel' => "apple-touch-icon-precomposed",
            'href' => Yii::$app->s3->createUrl($icons['s_114x114']),
            'sizes' => "72x72",
        ]);
        $this->registerLinkTag([
            'rel' => "apple-touch-icon-precomposed",
            'href' => Yii::$app->s3->createUrl($icons['s_144x144']),
            'sizes' => "144x144",
        ]);
    }

    public function userCan($attribute)
    {

        if (is_string($attribute)) {
            $prefix = $this->context->module->id .'_'. $this->context->id .'_'. $this->context->module->module->requestedAction->id;

            $fieldName = strtolower($prefix . '#' . $attribute);

            $auth = Yii::$app->authManager;

            if ($auth->getField($fieldName) === null) {
                $field = $auth->createField($fieldName);
                $field->display_name = strtoupper($fieldName);
                $auth->add($field);

            }

            return Yii::$app->user->can($fieldName);
        }
        
        return false;
    }
}
