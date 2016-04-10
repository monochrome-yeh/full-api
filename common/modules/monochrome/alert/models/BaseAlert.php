<?php

namespace common\modules\monochrome\alert\models;

use Yii;
use yii\mongodb\ActiveRecord;
use common\modules\monochrome\alert\Alert;

/**
 * This is the model class for collection "alert_item".
 *
 * @property \MongoId|string $_id
 * @property mixed $date
 * @property mixed $category
 * @property mixed $assign_item
 * @property mixed $type
 * @property mixed $type_item
 */
class BaseAlert extends ActiveRecord
{
    const CATEGORY_ORDER_ALERT_FOR_DEPOSIT = 1;
    const CATEGORY_ORDER_ALERT_FOR_DOWN_PAYMENT = 2;
    const CATEGORY_TO_DO_LIST_ALERT = 3;
    const CATEGORY_GUEST_BIRTHDAY_ALERT = 4;

    const TYPE_USER = 1;
    const TYPE_VENDOR = 2;
    const TYPE_PROJECT = 3;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'alert_item';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'date',
            'category',
            'assign_item',
            'type',
            'type_item',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'category', 'assign_item', 'type', 'type_item'], 'required'],
            ['category', 'in', 'range' => [
                self::CATEGORY_ORDER_ALERT_FOR_DEPOSIT,
                self::CATEGORY_ORDER_ALERT_FOR_DOWN_PAYMENT,
                self::CATEGORY_TO_DO_LIST_ALERT,
                self::CATEGORY_GUEST_BIRTHDAY_ALERT,
            ]],
            ['type', 'in', 'range' => [self::TYPE_USER, self::TYPE_VENDOR, self::TYPE_PROJECT]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Alert::t('app', 'ID'),
            'date' => Alert::t('app', 'Date'),
            'category' => Alert::t('app', 'Category'),
            'assign_item' => Alert::t('app', 'Assign Item'),
            'type' => Alert::t('app', 'Type'),
            'type_item' => Alert::t('app', 'Type Item'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (Alert::isEnable()) {
                return true;
            }
        }

        return false;
    }
}
