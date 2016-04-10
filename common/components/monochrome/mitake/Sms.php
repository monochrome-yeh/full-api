<?php

namespace common\components\monochrome\mitake;

use Yii;

// use common\components\monochrome\mitake\models\SmsModel;
// $sms = new SmsModel;
// $sms->body = "常數測試\r\n試試看Utf8中文如何.\r\n這是換行\r\ninfo.soldwoo.com";
// $sms->phone = '0953033616';

// Yii::$app->sms->send($sms);
// echo Yii::$app->sms->getPoint();exit;

class Sms extends \yii\base\Component
{

    public $account;

    public $password;

    public $enable = true;

    const URL = 'http://smexpress.mitake.com.tw:9600/';

    const SEND_API = 'SmSendGet.asp';

    const POINT_API = 'SmQueryGet.asp';

    private $_status;

    public function send($smsModel) {

        if ($smsModel instanceof \common\components\monochrome\mitake\models\SmsModel && $smsModel->validate()) {
            $url = self::URL.self::SEND_API."?username={$this->account}&password={$this->password}&dstaddr={$smsModel->phone}&DestName=葉先生&smbody={$smsModel->body}&encoding=UTF8";
             
            $ch = curl_init();
             
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $this->_status = curl_exec($ch);
             
            curl_close($ch);    

            return $this->_status;
        }    

        return false;
    }   

    public function getPoint() {
        $url = self::URL.self::POINT_API."?username={$this->account}&password={$this->password}";
        $ch = curl_init();
         
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $this->_status = curl_exec($ch);

        curl_close($ch);

        if (preg_match("/^accountpoint\=/", strtolower($this->_status))) {
           return (int)preg_replace("/^accountpoint\=/", '', strtolower($this->_status));
        }

        return 0;
    }    

    public function getProjectPoint($projectSuperModel) {
        if ($projectSuperModel instanceof \frontend\modules\project_management\monochrome\project\models\ProjectSuper) {

            $url = self::URL.self::POINT_API."?username={$projectSuperModel->sms_setting['account']}&password={$projectSuperModel->sms_setting['password']}";
            $ch = curl_init();
             
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $this->_status = curl_exec($ch);

            curl_close($ch);

            if (preg_match("/^accountpoint\=/", strtolower($this->_status))) {
               return (int)preg_replace("/^accountpoint\=/", '', strtolower($this->_status));
            }

            //TODO 未來這邊應該再做一個Project 點數不夠，是否找Vendor 有共用點數
        }
        return 0;
    }                   

}
