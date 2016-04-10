<?php
namespace common\modules\monochrome\members\components\auth;

use common\modules\monochrome\members\components\auth;
use common\modules\monochrome\members\models\FacebookUser; 

class FacebookLogin extends LoginAbstract{
	private $_model;
    private $_fbid = 0;

	public function __construct ($model) {
        if (empty($model)) {
            $model = new FacebookUser();
        }

        $this->_model = $model;
	}

	public function login ($attributes = null) {
        if ($attributes !== null && get_class($this->_model) == "app\modules\monochrome\members\models\FacebookUser") {
            if ($this->_model->isNewRecord) {
                $this->_model->login = ['facebook' => ['account'=> $attributes['id']]];
                $this->_model->detail = ['email' => [$attributes['email']]];
                $this->_model->status = 10;
            }
            else {
                if (!in_array($attributes['email'], $this->_model->detail['email'])) {
                    array_push($this->_model->detail['email'],$attributes['email']);
                }
            }
            //$this->_model->username = $attributes['name'];
        }

        if ($this->_model->save() && \Yii::$app->user->login($this->_model, 0)) {
            // $cookie = new \yii\web\Cookie([
            //     'name' => 'safe_user_info',
            //     'value' => 'Me want cookie!',
            //     'expire' => time() + 86400 * 365,
            // ]);
            // \Yii::$app->getResponse()->getCookies()->add($cookie);
            \Yii::$app->session->set('user.username',$attributes['name']);
            \Yii::$app->session->set('user.fbid',$attributes['id']);
            \Yii::$app->session->set('user.locale',$attributes['locale']);
            //\Yii::$app->session->get('user.some_attribute');
            return true;
        }


    	return false;
	}
}