<?php

namespace common\components\mark;

use yii\base\Widget;

class DateSelectorWidget extends Widget
{
	public $model;
	public $method = 'post';
	public $week_start = 1; //1是星期一 0是星期天
	public $buttons = ['day', 'week', 'month'];
	public $singleDateSelect = true;
	public $in_form = false;
	public $max_date = 'new Date()';

	public function init()
	{
		parent::init();
	}
	
	public function run()
	{

		if($this->in_form === false) {
			return $this->render('date', [
				'dateModel' => $this->model,
				'method' => $this->method,
				'week_start' => $this->week_start,
				'buttons' => $this->buttons,
				'singleDateSelect' => $this->singleDateSelect,
				'max_date' => $this->max_date,
			]);			
		}
		else {
			return $this->render('date_widget', [
				'dateModel' => $this->model,
				'week_start' => $this->week_start,
				'buttons' => $this->buttons,
				'max_date' => $this->max_date,
			]);			
		}

	}
}
