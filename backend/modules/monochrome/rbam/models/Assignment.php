<?php

namespace backend\modules\monochrome\rbam\models;

use backend\modules\monochrome\rbam\RBAM;
use Yii;

/**
 * This is the model class for collection "item_child".
 *
 * @property \MongoId|string $_id
 * @property mixed $name
 * @property mixed $children
 */
class Assignment extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'rbac_assignment';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            'user_id',
            'item_name',
            'created_at',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => RBAM::t('app', 'ID'),
            'user_id' => RBAM::t('app', 'User Id'),
            'item_name' => RBAM::t('app', 'Name'),
            'created_at' => RBAM::t('app', 'created at'),
        ];
    }
}
