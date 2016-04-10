<?php

namespace backend\modules\monochrome\rbam\models;

use Yii;

/**
 * This is the model class for collection "item_child".
 *
 * @property \MongoId|string $_id
 * @property mixed $name
 * @property mixed $children
 */
class ItemChild extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'rbac_item_child';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'children',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'children'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'children' => Yii::t('app', 'Children'),
        ];
    }
}
