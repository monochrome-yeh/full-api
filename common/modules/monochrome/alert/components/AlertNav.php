<?php

namespace common\modules\monochrome\alert\components;

use Yii;
use yii\base\Widget;
use common\modules\monochrome\alert\Alert;

class AlertNav extends Widget
{
	private $_items = [];
    public $items = [];

	public function init()
	{
		parent::init();

        if (\common\modules\monochrome\members\modules\vendorLimit\VendorLimit::checkVendorAccessForDisplay('alert')) {
            Alert::enable();

            if (is_array($this->items)) {
                foreach ($this->items as $item) {
                    $this->verify($item);
                }
            }
        } else {
            Alert::disable();
        }
	}

    private function verify($json)
    {
        if (is_string($json)) {
            $alertWidget = json_decode($json, true);
            if (is_array($alertWidget) && json_last_error() === JSON_ERROR_NONE && $alertWidget !== null) {
                $this->_items[] = $alertWidget;
            }
        }
    }

	public function run()
	{
		return $this->render('alert', [
			'items' => $this->_items,
		]);
	}
}
