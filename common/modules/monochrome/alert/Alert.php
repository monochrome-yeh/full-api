<?php

namespace common\modules\monochrome\alert;

use Yii;

class Alert extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\monochrome\alert\controllers';

    public $max = 30;

    public $order = [
        'alertDepositDay' => 10,
        'alertDownPaymentDay' => 5,
    ];

    public $guestBirthday = 5;

    private static $isEnable = true;

    public function getMax()
    {
        return (int)$this->max;
    }

    public function getDefaultOrderAlertDay()
    {
        return [
            'deposit_day' => (int)$this->order['alertDepositDay'],
            'down_payment_day' => (int)$this->order['alertDownPaymentDay'],
        ];
    }

    public function getDefaultGuestBirthdayAlertDay()
    {
        return (int)$this->guestBirthday;
    }

    public static function isEnable()
    {
        return self::$isEnable;
    }

    public static function enable()
    {
        self::$isEnable = true;
    }

    public static function disable()
    {
        self::$isEnable = false;
    }

    public function init()
    {
        parent::init();
    }

    private static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/alert/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@common/modules/monochrome/alert/messages',
            'fileMap' => [
                'modules/monochrome/alert/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('modules/monochrome/alert/' . $category, $message, $params, $language);
    }
}
