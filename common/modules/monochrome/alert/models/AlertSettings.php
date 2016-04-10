<?php

namespace common\modules\monochrome\alert\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\mongodb\ActiveRecord;
use common\modules\monochrome\alert\Alert;
use frontend\modules\project_management\monochrome\order\models\Order;

class AlertSettings extends AttributeBehavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
        ];
    }

    public function beforeSave($event)
    {
        $settings = $event->sender->settings;
        $settings['alert']['deposit_day'] = $event->sender->deposit_day;
        $settings['alert']['down_payment_day'] = $event->sender->down_payment_day;
        $settings['alert']['guest_birthday'] = $event->sender->guest_birthday;
        $event->sender->settings = $settings;
    }

    public function afterFind($event)
    {
        $settings = $event->sender->settings;
        $event->sender->deposit_day = isset($settings['alert']['deposit_day']) ? $settings['alert']['deposit_day'] : Yii::$app->getModule('alert')->getDefaultOrderAlertDay()['deposit_day'];
        $event->sender->down_payment_day = isset($settings['alert']['down_payment_day']) ? $settings['alert']['down_payment_day'] : Yii::$app->getModule('alert')->getDefaultOrderAlertDay()['down_payment_day'];
        $event->sender->guest_birthday = isset($settings['alert']['guest_birthday']) ? $settings['alert']['guest_birthday'] : Yii::$app->getModule('alert')->getDefaultGuestBirthdayAlertDay();
    }

    /**
     * @inheritdoc
     */
    public static function attributeLabels()
    {
        return [
            'deposit_day' => Alert::t('app', '{order_status} Alert Day', ['order_status' => Order::getStatusCatalog()[Order::STATUS_DEPOSIT]]),
            'down_payment_day' => Alert::t('app', '{order_status} Alert Day', ['order_status' => Order::getStatusCatalog()[Order::STATUS_DOWN_PAYMENT]]),
            'guest_birthday' => Alert::t('app', 'Guest Birthday Alert Day'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function rules($model)
    {
        return [
            [['deposit_day', 'down_payment_day', 'guest_birthday'], 'required'],
            [['deposit_day', 'down_payment_day', 'guest_birthday'], 'filter', 'filter' => 'intval'],
            [['deposit_day', 'down_payment_day', 'guest_birthday'], 'integer'],
            [['deposit_day', 'down_payment_day', 'guest_birthday'], 'compare', 'compareValue' => 0, 'operator' => '>', 'message' => Alert::t('app', 'Alert day must be 0 or more.')],
            [['deposit_day', 'down_payment_day', 'guest_birthday'], 'compare', 'compareValue' => Yii::$app->getModule('alert')->getMax(), 'operator' => '<=', 'message' => Alert::t('app', 'Guest Birthday Alert must less than or equal to {max}', ['max' => Yii::$app->getModule('alert')->getMax()])],
        ];
    }
}
