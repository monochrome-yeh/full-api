<?php

namespace common\components\mark;

use Yii;
use \DateTime;
use \DateTimeZone;
use yii\behaviors\AttributeBehavior;
use yii\mongodb\ActiveRecord;
use yii\base\ErrorException;

class MongoTimeFormat extends AttributeBehavior
{
    public function events()
    {
        return array_fill_keys(array_keys($this->attributes), 'evaluateAttributes');
    }

    public function evaluateAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = (array) $this->attributes[$event->name];
            foreach ($attributes as $format => $attribute) {
                $this->owner->$attribute = new \MongoDate(strtotime($this->owner->$attribute));
            }
        }
    }
}
