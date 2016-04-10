<?php

namespace common\modules\monochrome\alert\components;

use Yii;
use yii\mongodb\ActiveRecord;
use common\modules\monochrome\alert\models\BaseAlert;
use common\modules\monochrome\request\models\ToDoList;

class ToDoListAlertBehavior extends AlertBehavior
{
    public function events()
    {
        return array_merge(parent::events(), [
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ]);
    }

    public function afterInsert($event)
    {
        $model = new BaseAlert();

        // $model->date = $event->sender->deadline;
        $model->date = $event->sender->created_at;
        $model->category = BaseAlert::CATEGORY_TO_DO_LIST_ALERT;
        $model->assign_item = (string)$event->sender->_id;
        $model->type = BaseAlert::TYPE_USER;
        $model->type_item = $event->sender->assign_users;

        $model->save();
    }

    public function afterUpdate($event)
    {
    	$model = BaseAlert::find()->where([
    		'category' => BaseAlert::CATEGORY_TO_DO_LIST_ALERT,
    		'assign_item' => (string)$event->sender->_id,
    		'type' => BaseAlert::TYPE_USER
    	])->one();

    	if ($model == null) {
    		$this->afterInsert($event);
    		return true;
    	}

    	if ($event->sender->status === ToDoList::STATUS_DONE) {
            $model->delete();
            return true;
        }

        // $model->date = $event->sender->deadline;
        $model->type_item = array_values(array_diff($event->sender->assign_users, $event->sender->done_users));

    	$model->save();
    }

    public function afterDelete($event)
    {
        $model = BaseAlert::find()->where([
            // 'date' => $event->sender->deadline,
            'date' => $event->sender->created_at,
            'category' => BaseAlert::CATEGORY_TO_DO_LIST_ALERT,
            'assign_item' => (string)$event->sender->_id,
            'type' => BaseAlert::TYPE_USER
        ])->one();

        if ($model != null) {
            $model->delete();
        }
    }
}
