<?php
namespace api\modules\monochrome\cv\controllers;

use common\modules\monochrome\members\models\User;
use common\modules\monochrome\members\models\AdminUser;
use common\modules\monochrome\members\models\CVModel;
use yii\web\NotFoundHttpException;
use yii\rest\Controller;
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
	    	[
		        'corsFilter' => [
		            'class' => \yii\filters\Cors::className(),
		            'cors' => [
		                // restrict access to
		                'Origin' => ['http://dev.monochrome.com'],
		                'Access-Control-Request-Method' => ['GET', 'HEAD', 'OPTIONS', 'POST', 'PUT'],
		                // Allow only POST and PUT methods
		                'Access-Control-Request-Headers' => ['X-Requested-With', 'X-Monochrome-CV', 'Content-Type'],
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
		            ]

		        ]
	        ],
	        parent::behaviors(),  
	        [
		        'contentNegotiator' => [
		            'class' => \yii\filters\ContentNegotiator::className(),
		            //'only' => ['test', 'view'],
		            'formatParam' => '_format',
		            'formats' => [
		                'application/json' => \yii\web\Response::FORMAT_JSON,
		                //'application/xml' => \yii\web\Response::FORMAT_XML,
		            ],
		        ]
	        ]
	    );
	}

    public function actionProfile($uid) {
    	$data = User::find()->where(['_id' => $uid])->select([
    		'_id' => false,
    		'cv.zh_tw.skill_details' => false,
    		'login' => false,
    		'tags' => false,
    		'status' => false,
    		'auth_key' => false,
    		'login_fail' => false,
    		'username' => false,
    		'created_at' => false,
    		'updated_at' => false,
    	])->asArray()->one();
    	return $data['cv']['zh_tw'];
    }
    public function actionSkillDetails($uid) {
    	$data = User::find()->where(['_id' => $uid])->select(['_id' => false, 'cv.zh_tw.skill_details'])->asArray()->one();
    	return $data['cv']['zh_tw'];
    }

    public function actionUpdate($uid)
    {
        $userModel = $this->findUserModel($uid);
        if (!isset($userModel->cv)) {
            return false; //no user, no cv
        }
        else {
            $model = new CVModel();
            $data = [];
            $data['CVModel'] = $userModel->cv['zh_tw'];
            $model->load($data);
        }


        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $cv = $userModel->cv;
            $cv['zh_tw'] = $model->attributes;
            $userModel->cv = $cv;

            if($userModel->save()) {
                return $userModel->cv['zh_tw']; 
            }
            else {
            	return $userModel->getErrors();
            }
        } else {
       		//return $model->getErrors();
       		return $userModel->cv['zh_tw'];
        }
        
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
