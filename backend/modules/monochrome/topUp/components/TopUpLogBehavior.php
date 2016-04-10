<?php

namespace backend\modules\monochrome\topUp\components;

use Yii;
use yii\base\Behavior;
use yii\mongodb\ActiveRecord;
use backend\modules\monochrome\topUp\TopUp;
use backend\modules\monochrome\topUp\models\ProjectTopUpLog;

class TopUpLogBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    public function afterSave($event)
    {
    	$model = new ProjectTopUpLog();

    	$model->vid = $event->sender->vid;
    	$model->pid = (string)$event->sender->_id;
    	$model->log_content = TopUp::t('app', 'Active Date：{active_date}。Expire Date：{expire_date}。', [
    		'active_date' => Yii::$app->formatter->asDate($event->sender->active_date),
    		'expire_date' => Yii::$app->formatter->asDate($event->sender->expire_date),
    	]);

    	$model->save();
    }
}
