<?php

namespace common\modules\monochrome\taxonomy\models;

use Yii;
use \MongoId;
use yii\mongodb\ActiveRecord;
use yii\web\NotFoundHttpException;
use common\modules\monochrome\members\models\Vendor;
use common\modules\monochrome\taxonomy\Taxonomy;

/**
 * This is the model class for collection "taxonomy_type".
 *
 * @property \MongoId|string $_id
 * @property mixed $name
 * @property mixed $fields
 */
class Type extends ActiveRecord
{
    private static $_type_list;
    private static $_typeListByVendor;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'taxonomy_type';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'unique_name',
            'name',
            'fields',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unique_name', 'name'], 'required'],
            ['unique_name', 'unique'],
            ['fields', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Taxonomy::t('app', 'ID'),
            'name' => Taxonomy::t('app', 'Type Name'),
            'fields' => Taxonomy::t('app', 'Fields'),
        ];
    }

    public static function getTypeList()
    {
        if (self::$_type_list == null) {
            $result = [];

            $types = static::find()->asArray()->select(['name'])->all();
            if (!empty($types)) {
                foreach ($types as $type) {
                    $result[(string)$type['_id']] = $type['name'];
                }
            }

            self::$_type_list = $result;
        }

        return self::$_type_list;
    }

    public static function getTypeListByVendor($vendor)
    {
        if (self::$_typeListByVendor == null) {
            if ($vendor != null && $vendor instanceof \common\modules\monochrome\members\models\Vendor) {
                $result = [];
                $cms_type = $vendor->settings['cms']['list'];
                if (!empty($cms_type)) {
                    $mongo_cms_type_id = [];
                    foreach ($cms_type as $id) {
                        $mongo_cms_type_id[] = new MongoId($id);
                    }

                    $types = static::find()->where(['_id' => ['$in' => $mongo_cms_type_id]])->asArray()->select(['name', 'unique_name'])->all();

                    foreach ($types as $type) {
                        $result[$type['unique_name']] = [
                            'name' => $type['name'],
                            'id' => (string)$type['_id'],
                        ];
                    }
                }

                self::$_typeListByVendor = $result;
            }
        }

        return self::$_typeListByVendor;
    }
}
