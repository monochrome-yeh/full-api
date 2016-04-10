<?php

namespace common\components\monochrome\search_form;

use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Collapse;

class SearchForm extends ActiveForm
{   

	public $action;

	public $method = 'get';

	public $cid;



	public function init() {
		if($this->cid == null) {
			$this->cid = 'c'.uniqid();
		}
		echo $this->render('begin', ['cid' => $this->cid, 'id' => $this->getId()]);
		//default add pjax
		$this->options = array_merge($this->options, ['data-pjax' => '']);

		parent::init();
	}

    public function run()
    {
    	parent::run();
    	echo $this->render('end');
    }

    private function is_searching_date() {
    	if(Yii::$app->request->get('monochrome_date_search') == 1) {
    		//echo 123;exit;
    	}
    }
}