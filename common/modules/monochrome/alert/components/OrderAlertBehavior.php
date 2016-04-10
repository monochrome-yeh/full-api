<?php

namespace common\modules\monochrome\alert\components;

use Yii;
use yii\mongodb\ActiveRecord;
use common\modules\monochrome\alert\models\BaseAlert;
use frontend\modules\project_management\monochrome\order\models\Order;

class OrderAlertBehavior extends AlertBehavior
{
    public function afterInsert($event)
    {
        $model = new BaseAlert();

        switch ($event->sender->status) {
            case Order::STATUS_DEPOSIT:
                $model->date = $event->sender->status_deposit_date;
                $model->category = BaseAlert::CATEGORY_ORDER_ALERT_FOR_DEPOSIT;
                break;
            case Order::STATUS_DOWN_PAYMENT:
                $model->date = $event->sender->status_down_payment_date;
                $model->category = BaseAlert::CATEGORY_ORDER_ALERT_FOR_DOWN_PAYMENT;
                break;
            default:
                return true;
        }

        $model->assign_item = (string)$event->sender->_id;
        $model->type = BaseAlert::TYPE_PROJECT;
        $model->type_item = $event->sender->pid;
        $model->save();
    }

    public function afterUpdate($event)
    {
    	$model = BaseAlert::find()->where([
    		'category' => ['$in' => [BaseAlert::CATEGORY_ORDER_ALERT_FOR_DEPOSIT, BaseAlert::CATEGORY_ORDER_ALERT_FOR_DOWN_PAYMENT]],
    		'assign_item' => (string)$event->sender->_id,
    		'type' => BaseAlert::TYPE_PROJECT,
    		'type_item' => $event->sender->pid
    	])->one();

    	if ($model == null) {
    		$this->afterInsert($event);
    		return true;
    	}

    	switch ($event->sender->status) {
    		case Order::STATUS_DEPOSIT:
                $model->date = $event->sender->status_deposit_date;
                $model->category = BaseAlert::CATEGORY_ORDER_ALERT_FOR_DEPOSIT;
    			break;
            case Order::STATUS_DOWN_PAYMENT:
                $model->date = $event->sender->status_down_payment_date;
                $model->category = BaseAlert::CATEGORY_ORDER_ALERT_FOR_DOWN_PAYMENT;
                break;
            default:
            	$model->delete();
                return true;
    	}

    	$model->save();
    }
}
