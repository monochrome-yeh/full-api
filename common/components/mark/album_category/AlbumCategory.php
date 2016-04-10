<?php

namespace common\components\mark\album_category;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\components\mark\PriorityModel;

/**
 * This is the model class for collection "album_category".
 *
 * @property \MongoId|string $_id
 * @property mixed $vid
 * @property mixed $uid
 * @property mixed $catalog
 * @property mixed $title
 * @property mixed $target
 * @property mixed $target_id
 * @property mixed $priority
 * @property mixed $status
 * @property mixed $created_at
 * @property mixed $updated_at
 */
abstract class AlbumCategory extends PriorityModel
{
    private static $_album_category;

    const TARGET_PROJECT = 1;

    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;

    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'album_category';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), [
            '_id',
            'vid',
            'uid',
            'catalog',
            'title',
            'target',
            'target_id',
            'status',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            '_id' => Yii::t('common/commission_agents', 'ID'),
            'vid' => Yii::t('common/commission_agents', 'Vid'),
            'uid' => Yii::t('common/commission_agents', 'Uid'),
            'catalog' => Yii::t('common/commission_agents', 'Album Category Catalog'),
            'title' => Yii::t('common/commission_agents', 'Album Category Title'),
            'target' => Yii::t('common/commission_agents', 'Album Category Target'),
            'target_id' => Yii::t('common/commission_agents', 'Album Category Target Id'),
            'status' => Yii::t('common/commission_agents', 'Album Category Status'),
            'created_at' => Yii::t('common/app', 'Created At'),
            'updated_at' => Yii::t('common/app', 'Updated At'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['title', 'unique_title'],
            [['target', 'status'], 'filter', 'filter' => 'intval'],
            ['target', 'in', 'range' => [self::TARGET_PROJECT]],
            ['status', 'in', 'range' => [self::STATUS_DISABLE, self::STATUS_ENABLE]],
            [['catalog', 'title', 'target', 'target_id'], 'required'],
            [['vid', 'uid', 'created_at', 'updated_at'], 'safe'],
            ['title', 'trim'],
        ]);
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

    public function unique_title($attribute, $params)
    {
        if (static::find()->where(array_merge(static::basicQuery(), ['title' => $this->$attribute, '_id' => ['$ne' => $this->_id]]))->exists()) {
            $this->addError($attribute, Yii::t('common/commission_agents', 'Title {0} is not unique.', [$this->$attribute]));
        }
    }

    public static function getAlbumCategory()
    {
        if (self::$_album_category == null) {
            $result = [];

            foreach (static::find()->where(array_merge(static::basicQuery()))->select(['title'])->orderby('priority ASC')->asArray()->all() as $albumCategory) {
                $result[(string)$albumCategory['_id']] = $albumCategory['title'];
            }

            self::$_album_category = $result;
        }

        return self::$_album_category;
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_DISABLE => Yii::t('common/commission_agents', 'Album Category Disable'),
            self::STATUS_ENABLE => Yii::t('common/commission_agents', 'Album Category Enable'),
        ];
    }

    abstract public static function getCatalog();
}
