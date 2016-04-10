<?php

namespace common\modules\monochrome\members\controllers;

use Yii;
use yii\filters\AccessControl;
use backend\modules\monochrome\rbam\components\Controller;
use common\modules\monochrome\members\components\auth\FacebookLogin;
use common\modules\monochrome\members\models\FacebookUser;

class FacebookController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['auth'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],                  
                ],
            ],
        ];
    }

    public function fbSuccessCallback($client)
    {
      	$attributes = $client->getUserAttributes();

        $user = FacebookUser::findOne(['status' => FacebookUser::STATUS_ACTIVE, 'login.facebook.account' => $attributes['id']]);

        $fb = new FacebookLogin($user);
        $fb->login($attributes);
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'fbSuccessCallback'],
            ],
        ];
    }
}
