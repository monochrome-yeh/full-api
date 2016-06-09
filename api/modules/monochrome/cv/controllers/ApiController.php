<?php

namespace api\modules\monochrome\cv\controllers;

use common\modules\monochrome\members\models\User;
use common\modules\monochrome\members\models\CVModel;
use yii\web\NotFoundHttpException;
use common\components\monochrome\rest\Controller;
use Yii;
use yii\helpers\ArrayHelper;

class ApiController extends Controller
{
    public function actions()
    {
        $actions = parent::actions();
        $actions['options'] = [
            'class' => 'yii\rest\OptionsAction',
            // optional:
            //'collectionOptions' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
            //'resourceOptions' => ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        ];

        return $actions;
    }

    public function behaviors()
    {
        // $behaviors = parent::behaviors();
        // print_r($behaviors);exit;
        return ArrayHelper::merge(
            // parent::behaviors(),
            [
               'corsFilter' => [
                   'class' => \yii\filters\Cors::className(),
                   'cors' => [
                       // restrict access to
                       'Origin' => Yii::$app->params['header']['origin'],
                       'Access-Control-Request-Method' => ['GET', 'HEAD', 'OPTIONS', 'POST', 'PUT'],
                       // Allow only POST and PUT methods
                       'Access-Control-Request-Headers' => Yii::$app->params['header']['access-headers'],
                       // Allow only headers 'X-Wsse'
                       'Access-Control-Allow-Credentials' => true,
                       // Allow OPTIONS caching
                       //'Access-Control-Max-Age' => 3600,
                       // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                       //'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                   ],
                   'actions' => [
                       'profile' => [
                           'Access-Control-Allow-Credentials' => false,
                             'Access-Control-Request-Method' => ['GET'],
                       ],
                       'skill-details' => [
                           'Access-Control-Allow-Credentials' => false,
                             'Access-Control-Request-Method' => ['GET'],
                       ],

                       'update' => [
                           'Access-Control-Allow-Credentials' => true,
                             'Access-Control-Request-Method' => ['PUT'],
                       ],
                   ],

               ],
            ],
            [
               'contentNegotiator' => [
                   'class' => \yii\filters\ContentNegotiator::className(),
                   //'only' => ['test', 'view'],
                   'formatParam' => '_format',
                   'formats' => [
                       'application/json' => \yii\web\Response::FORMAT_JSON,
                       //'application/xml' => \yii\web\Response::FORMAT_XML,
                    ],
               ],
            ]
        );
    }

    public function actionProfile($uid, $language)
    {
        $data = $this->findUserCV($uid);

        $result = $data['cv']['zh_tw'];

        if ($language !== 'zh_tw' && isset($data['cv'][$language])) {
            $result = array_replace_recursive($data['cv']['zh_tw'], $data['cv'][$language]);
        }

        return $result;
    }
    // public function actionSkillDetails($uid, $language) {
    //     $data = User::find()->where(['_id' => $uid])->select(['_id' => false, 'cv.'.$language.'.skill_details'])-> asArray()->one();
    //     return $data['cv'][$language];
    // }

    // public function actionIntroductionDetail($uid, $language) {
    //     $data = User::find()->where(['_id' => $uid])->select(['_id' => false, 'cv.'.$language.'.introduction_detail'])-    >asArray()->one();
    //     return $data['cv'][$language];
    // }

    public function actionUpdate($uid, $language)
    {
        $userModel = $this->findUserModel($uid);
        if (!isset($userModel->cv)) {
            return false; //no user, no cv
        } else {
            $model = new CVModel();
            $data = [];
            $data['CVModel'] = $userModel->cv['zh-tw'];
            if ($language !== 'zh_tw' && isset($userModel->cv[$language])) {
                $data['CVModel'] = array_replace_recursive($userModel->cv['zh_tw'], $userModel->cv[$language]);
            }
            $model->load($data);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $cv = $userModel->cv;
            $cv[$language] = $model->attributes;
            $userModel->cv = $cv;

            if ($userModel->save()) {
                return $userModel->cv[$language];
            } else {
                return $userModel->getErrors();
            }
        } else {
            //return $model->getErrors();
            return $userModel->cv[$language];
        }
    }

    protected function findUserCV($uid)
    {
        return User::find()->where(['_id' => $uid])->select([
            '_id' => false,
            'cv' => true,
        ])->asArray()->one();
    }

    protected function findUserModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common/app', 'The requested page does not exist.'));
        }
    }
}
