<?php

namespace common\modules\monochrome\taxonomy\components;

use Yii;
use yii\base\ActionFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\modules\monochrome\taxonomy\models\Type;

class TypeAccessControl extends ActionFilter
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $type = Yii::$app->request->get('type');

            if ($type != null) {
                $model = Type::find()->where(['unique_name' => $type])->one();

                if ($model == null) {
                    throw new NotFoundHttpException(Yii::t('common/app', 'The requested page does not exist.'));
                }

                if (!Yii::$app->user->isGuest) {
                    if (array_key_exists($type, Type::getTypeListByVendor(Yii::$app->user->getVendor()))) {
                        return true;
                    } else {
                        throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                    }
                } else {
                    return true;
                }    
            } else {
                return true;
            }    
        }

        return false;
    }
}
