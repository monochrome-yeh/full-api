<?php

namespace common\components\mark;

use Yii;

class DayRange
{
	private static $_dayRange;

    private function _getDayRange($time)
    {
    	if (self::$_dayRange == null) {
	        $result = [];

	        if (is_int($time)) {
	            $result['begin'] = strtotime(Yii::$app->formatter->asDate($time));
	            $result['end'] = $result['begin'] + 86399;
	        }

	        self::$_dayRange = $result;
    	}

    	return self::$_dayRange;
    }

    public static function getDayRange($time = null)
    {
    	$queryTime = ($time === null ? time() : $time);
    	return self::_getDayRange($queryTime);
    }
}
