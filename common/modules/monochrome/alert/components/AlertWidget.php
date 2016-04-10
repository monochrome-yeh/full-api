<?php

namespace common\modules\monochrome\alert\components;

use Yii;
use yii\base\Widget;
use common\modules\monochrome\alert\Alert;

abstract class AlertWidget extends Widget
{
	public $title;
	public $class;
	public $style;
	public $roleScope;

	protected $total = 0;
	protected $items = [];
	abstract protected function setAlertInfo();

	public function init()
	{
		parent::init();

		if (Alert::isEnable()) {
			$roleScope = (array)$this->roleScope;
			if ($roleScope != null && in_array(isset(Yii::$app->user->identity->role) ? Yii::$app->user->identity->role : '', $roleScope)) {
				$this->setAlertInfo();
				$this->setItemOptions();
			}
		}
	}

	private function setItemOptions()
	{
		if (is_array($this->items) && $this->items != null) {
			foreach ($this->items as &$item) {
				$result = [
					'class' => $this->getClass(),
					'style' => $this->getStyle(),
				];

				if (isset($item['options']['class']) && $item['options']['class'] != null) {
					$result['class'] .= ' '.$item['options']['class'];
				}

				if (isset($item['options']['style']) && $item['options']['style'] != null) {
					$result['style'] .= $item['options']['style'];
				}

				$item['options'] = $result;
			}
		}
	}

	private function getClass()
	{
		$default = 'alert-default-class';

		if ($this->class != null) {
			return $default.' '.$this->class;
		}

		return $default;
	}

	private function getStyle()
	{
		$default = 'border:1px solid #e7eaec;margin:0;';

		return $default.$this->style;
	}

	private function getTotal()
	{
		return $this->total;
	}

	private function getItems()
	{
		return $this->items;
	}

	public function run()
	{
		if ($this->getTotal() > 0) {
			return json_encode([
				'label' => '<div class="count-info">'.$this->title.'<span class="label label-danger">'.$this->getTotal().'</span></div>',
				'items' => $this->getItems(),
				'options' => [
					'class' => 'alert-widget-li',
				],
			]);
		}
	}
}
