<?php

namespace common\modules\monochrome\members\controllers;

use Yii;
use yii\web\Cookie;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\components\mark\trace\ActionTrace;
use common\modules\monochrome\members\models\User;
use common\modules\monochrome\members\models\SecurityForm;
use backend\modules\monochrome\rbam\components\Controller;
use backend\modules\monochrome\rbam\components\AccessControl;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['logout', 'security'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'actionTrace' => [
                'class' => ActionTrace::className(),
                'categories' => [
                    'action_trace' => ['*'],
                ],
            ]
        ];
    }

    public function actionLogout()
    {
        if (isset(Yii::$app->user->getIdentity()->vid)) {
            $vid = Yii::$app->user->getIdentity()->vid;
            Yii::$app->user->logout();

            return $this->redirect(["/login/{$vid}"]);
        } else {
            Yii::$app->user->logout();

            return $this->goHome();
        }
    }

    public function actionSecurity()
    {
        $model = new SecurityForm();

        if ($model->load(Yii::$app->request->post()) && $model->security())
        {
            $uid = Yii::$app->user->getId();
            $cookie = new Cookie([
                'name' => "{$uid}-security",
                'value' => $uid,
                'expire' => time() + (int)Yii::$app->getModule('members')->securityUpdateExpair,
            ]);
            Yii::$app->getResponse()->getCookies()->add($cookie);

            return $this->redirect(urldecode(Yii::$app->request->get('b')));
        }
        else
        {
            return $this->render('security', [
                'model' => $model,
            ]);
        }
    }
}
