<?php

namespace common\modules\monochrome\members\components;

use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use common\modules\monochrome\members\models\Vendor;

class VendorAccessControl extends ActionFilter
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $vid = Yii::$app->request->get('vid');
            if ($vid != null) {
                try {
                    $mongoId = new \MongoId($vid);
                } catch(\MongoException $ex) {
                    $mongoId = '';
                }

                $model = Vendor::find()->where([
                    '$or' => [
                        ['_id' => $mongoId],
                        ['alias' => $vid],
                    ]
                ])->asArray()->select(['status'])->one();

                $vid = (string)$model['_id'];

                if ($model == null) {
                    throw new NotFoundHttpException(Yii::t('common/app', 'The requested page does not exist.'));
                }

                if (!Yii::$app->user->isGuest) {
                    if ($vid == Yii::$app->user->getIdentity()->vid && $model['status'] == 1) {
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
