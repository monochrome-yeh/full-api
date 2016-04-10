<?php

namespace common\modules\monochrome\alert\components;

use yii\base\Behavior;
use yii\mongodb\ActiveRecord;

abstract class AlertBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }

    abstract public function afterInsert($event);
    abstract public function afterUpdate($event);
}
