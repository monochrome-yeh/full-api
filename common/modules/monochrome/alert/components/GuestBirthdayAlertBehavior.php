<?php

namespace common\modules\monochrome\alert\components;

use Yii;
use yii\mongodb\ActiveRecord;
use common\modules\monochrome\alert\models\BaseAlert;
use frontend\modules\project_management\mark\guest\models\Guest;

class GuestBirthdayAlertBehavior extends AlertBehavior
{
    public function events()
    {
        return array_merge(parent::events(), [
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ]);
    }

    public function afterInsert($event)
    {
        $birthday = $event->sender->birthday;
        if (is_numeric($birthday)) {
            $model = new BaseAlert();

            $date['nd'] = (int)Yii::$app->formatter->asDate($birthday, 'php:nd');
            $date['m-d'] = Yii::$app->formatter->asDate($birthday, 'php:m-d');
            $model->date = $date;

            $model->category = BaseAlert::CATEGORY_GUEST_BIRTHDAY_ALERT;
            $model->assign_item = (string)$event->sender->_id;
            $model->type = BaseAlert::TYPE_PROJECT;
            $model->type_item = $event->sender->pid;

            $model->save();
        }
    }

    public function afterUpdate($event)
    {
    	$model = BaseAlert::find()->where([
    		'category' => BaseAlert::CATEGORY_GUEST_BIRTHDAY_ALERT,
    		'assign_item' => (string)$event->sender->_id,
    		'type' => BaseAlert::TYPE_PROJECT,
            'type_item' => $event->sender->pid,
    	])->one();

    	if ($model == null) {
    		$this->afterInsert($event);
    		return true;
    	}

        $birthday = $event->sender->birthday;
        if (is_numeric($birthday)) {
            $date['nd'] = (int)Yii::$app->formatter->asDate($birthday, 'php:nd');
            $date['m-d'] = Yii::$app->formatter->asDate($birthday, 'php:m-d');
            $model->date = $date;

        	$model->save();
        } else {
            $model->delete();
        }
    }

    public function afterDelete($event)
    {
        $birthday = $event->sender->birthday;
        if (is_numeric($birthday)) {
            $model = BaseAlert::find()->where([
                'date.nd' => (int)Yii::$app->formatter->asDate($birthday, 'php:nd'),
                'date.m-d' => Yii::$app->formatter->asDate($birthday, 'php:m-d'),
                'category' => BaseAlert::CATEGORY_GUEST_BIRTHDAY_ALERT,
                'assign_item' => (string)$event->sender->_id,
                'type' => BaseAlert::TYPE_PROJECT,
                'type_item' => $event->sender->pid,
            ])->one();

            if ($model != null) {
                $model->delete();
            }
        }
    }
}
