<?php

namespace common\components\mark;

use yii\base\Widget;

class AddressWidget extends Widget
{
	public $form;
	public $model;
	public $countyAttributeName;
	public $districtAttributeName;
	public $enableLabel = true;

	public function init()
	{
		parent::init();
	}
	
	public function run()
	{
		return $this->render('address', [
			'form' => $this->form,
			'model' => $this->model,
			'countyAttributeName' => $this->countyAttributeName,
			'districtAttributeName' => $this->districtAttributeName,
			'enableLabel' => $this->enableLabel,
		]);
	}
}
