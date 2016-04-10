<?php

namespace common\components\monochrome\models;

use \Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class DateSelectorForm extends Model
{
    public $fromDate;
    public $toDate;

    private $_is_limit = false;
    private $_limit_time = 0;

    public function init() {
        $currentYear = date('Y');
        $beginYear = "$currentYear-01-01";

        $this->fromDate = date('Y-m-d' ,strtotime($beginYear));
        $this->toDate = date('Y-m-d' ,time());
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['fromDate', 'toDate'], 'required'],
            [['fromDate', 'toDate'], 'date', 'skipOnEmpty' => false],
            [['fromDate', 'toDate'], 'default', 'value' => date('Y-m-d',time())],
            ['fromDate', 'compareDate'],
            ['toDate', 'checkLimit'],
        ];
    }

    public function attributeLabels() {
        return [
            'fromDate' => Yii::t('common/app', 'From Date'),
            'toDate' => Yii::t('common/app', 'To Date'),
        ];
    }

    public function compareDate($attribute, $params) {
        $fromTime = 0;
        $toTime = 0;

        if ($this->$attribute != null) {
            $fromTime = Yii::$app->formatter->asTimestamp($this->$attribute);
        }

        if ($this->toDate != null) {
            $toTime = Yii::$app->formatter->asTimestamp($this->toDate);
        }

        if ($toTime < $fromTime) {
            $this->addError('toDate', Yii::t('common/app', "Can not Below From Date."));
        }
    }

    public function fromTime() {
        return strtotime($this->fromDate);
    }

    public function toTime() {
        return strtotime($this->toDate);
    }

    public function setLimit($limit_time) {
        if ($limit_time > 0) {
            $this->_is_limit = true;
            $this->_limit_time = $limit_time;
        }

        return $this;
    }

    public function lastWeek(){
        $previous_week = strtotime("-1 week +1 day");

        $start_week = strtotime("last monday midnight",$previous_week);
        $end_week = strtotime("next sunday",$start_week);

        $start_week = date("Y-m-d",$start_week);
        $end_week = date("Y-m-d",$end_week);
        $this->fromDate = $start_week;
        $this->toDate = $end_week;        
    }

    public function lastMonth(){
        $this->fromDate = date("Y-m-d", strtotime("first day of this month"));
        $this->toDate = date("Y-m-d", strtotime("last day of this month"));
    }

    public function checkLimit($attribute, $params){
        if ($this->_is_limit) {
            if (abs((int)$this->toTime() - (int)$this->fromTime()) > $this->_limit_time) {
                $this->addError($attribute, Yii::t('common/app', 'Date out of range.'));
            }
        }
    }
}
