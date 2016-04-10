<?php

namespace common\modules\monochrome\taxonomy\models;

use Yii;
use \MongoId;
use yii\behaviors\TimestampBehavior;
use common\modules\monochrome\taxonomy\Taxonomy;
use common\modules\monochrome\taxonomy\models\Type;
use common\modules\monochrome\members\models\Vendor;

/**
 * This is the model class for collection "taxonomy_item".
 *
 * @property \MongoId|string $_id
 * @property mixed $type
 * @property mixed $name
 */
class Item extends \yii\mongodb\ActiveRecord
{
    private static $_itemStorage;
    private static $_itemsByVendor;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'taxonomy_item';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'vid',
            'spec_id',
            'type',
            'name',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['spec_id', 'unique' , 'targetAttribute' => ['spec_id', 'vid']],
            [['vid', 'type', 'name'], 'required'],
            ['type', 'check_type'],
            ['name', 'trim'],
        ];
    }

    public function check_type($attribute, $params)
    {
        $vendor = Vendor::findOne($this->vid);

        $types = [];
        if ($vendor != null) {
            $types = array_keys(Type::getTypeListByVendor($vendor));
        }

        if (!in_array($this->$attribute, $types)) {
            $this->addError($attribute, Taxonomy::t('app', 'Wrong Type'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Taxonomy::t('app', 'ID'),
            'type' => Taxonomy::t('app', 'Type'),
            'name' => Taxonomy::t('app', 'Item Name'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function getEdmAdMediaItem($vid)
    {
        $result = null;

        if (!empty($item = static::find()->where(['vid' => (string)$vid, 'type' => 'ad_media', 'name' => Yii::$app->getModule('taxonomy')->edmAdMediaName])->select(['_id'])->one())) {
            $result = (string)$item->_id;
        }

        return $result;
    }

    public static function getEdmGuestStatusItem($vid)
    {
        $result = null;

        if (!empty($item = static::find()->where(['vid' => (string)$vid, 'type' => 'guest_status', 'name' => Yii::$app->getModule('taxonomy')->edmGuestStatusName])->select(['_id'])->one())) {
            $result = (string)$item->_id;
        }

        return $result;
    }

    public static function getItemsByVendor($vendor, $type)
    {
        $legalTypeList = Type::getTypeListByVendor($vendor);

        if (self::$_itemsByVendor == null || !isset(self::$_itemsByVendor[$type])) {
            $result = [];

            if ($legalTypeList != null && array_key_exists($type, $legalTypeList)) {
                $items = static::findAll(['vid' => (string)$vendor->_id, 'type' => $type]);
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $result[(string)$item->_id] = $item->name;
                    }
                }
            }

            self::$_itemsByVendor[$type] = $result;
        }

        return self::$_itemsByVendor[$type];
    }

    public static function getItemName($items_id)
    {
        $result = [];

        if ($items_id != null) {
            $mongo_items_id = [];
            foreach ((array)$items_id as $id) {
                if ($id != null) {
                    if (isset(self::$_itemStorage[$id])) {
                        $result[] = self::$_itemStorage[$id];
                    } else {
                        if (MongoId::isValid($id)) {
                            $mongo_items_id[] = new MongoId($id);
                        }
                    }
                }
            }

            if ($mongo_items_id != null) {
                $items = static::find()->where(['_id' => ['$in' => $mongo_items_id]])->asArray()->select(['name'])->all();
                foreach ($items as $item) {
                    $result[] = $item['name'];
                    self::$_itemStorage[(string)$item['_id']] = $item['name'];
                }
            }
        }

        return $result;
    }
}
