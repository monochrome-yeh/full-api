<?php

namespace common\modules\monochrome\games\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\modules\monochrome\games\models\PokeModel;
use common\modules\monochrome\members\models\User;

class PokeController extends Controller
{
	public $layout ='main';

	public function init()
	{

	        Yii::$app->user->loginUrl = ['/members/default/auth','authclient' => 'facebook'];
	}


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['index, create'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create', 'confirm'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],                    
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'create' => ['post'],
                ],
            ]
        ];
    }	
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
    {
    	if (!in_array('pokeGame', is_array(\Yii::$app->user->getIdentity()->agree_terms)?$model->agree_terms:[])) {
    		$this->redirect('/games/poke/confirm');
    	}
    	$model = new PokeModel();
        return $this->render('create', ['model' => $model]);
    }

    public function actionConfirm() {
    	if (Yii::$app->request->getIsPost()) {
	    	$model->agree_terms = array_merge(empty($model->agree_terms)?[]:$model->agree_terms, ['pokeGame']);

	    	if ($model->save()) {

	    	}
    	}
    	$model = \Yii::$app->user->getIdentity();
    	if (in_array('pokeGame', is_array($model->agree_terms)?$model->agree_terms:[])) {
    		return $this->goBack();
    	}

        return $this->render('confirm', ['model' => $model]);
    }
}
