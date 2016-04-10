<?php

namespace common\components\mark;

use Yii;
use \DateTime;
use \DateTimeZone;
use yii\behaviors\AttributeBehavior;
use yii\mongodb\ActiveRecord;
use yii\base\ErrorException;

class TimeStandard extends AttributeBehavior
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
                $this->formatterSelector($format, (array)$attribute);
            }
        }
    }

    private function formatterSelector($format, array $attributes)
    {
        if (empty($format)) {
            $format = 'n';
        }

        try {
            foreach ($attributes as $attribute) {
                if (!empty($this->owner->$attribute)) {
                    switch ($format) {
                        case 'date':
                            if (is_numeric($this->owner->$attribute)) {
                                $this->owner->$attribute = date('Y-m-d', $this->owner->$attribute) ;
                            }

                            break;
                        case 'dateTime':
                            if (is_numeric($this->owner->$attribute) && $this->owner->$attribute > 0) {
                               $this->owner->$attribute = date('Y-m-d H:i:s', $this->owner->$attribute);
                            }

                            break;
                        default:
                            if ($this->validateDate($this->owner->$attribute)) {
                                $this->owner->$attribute = strtotime($this->owner->$attribute);
                            }

                            break;
                    }
                }
            }
        } catch(ErrorException $e) {
            Yii::warning("Date Formatter Error.");
        }
    }

    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
