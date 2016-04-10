<?php

namespace common\modules\monochrome\announcement\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\modules\monochrome\announcement\Announcement as AnnouncementModule;
/**
 * This is the model class for collection "announcement".
 *
 * @property \MongoId|string $_id
 * @property mixed $title
 * @property mixed $content
 * @property mixed $read
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class Announcement extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'announcement';
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
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'title',
            'content',
            'read',
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
            ['title', 'string', 'max' => 75],
            ['content', 'string', 'max' => 500],
            ['read', 'is_array'],
            [['title', 'content', 'read', 'created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('common/app', 'ID'),
            'title' => Yii::t('common/app', 'Title'),
            'content' => Yii::t('common/app', 'Content'),
            'read' => AnnouncementModule::t('app', 'Read'),
            'created_at' => Yii::t('common/app', 'Created At'),
            'updated_at' => Yii::t('common/app', 'Updated At'),
        ];
    }

    public static function unreadCount($uid) {
        return static::find()->where(['read' => ['$ne' => $uid]])->count();
    }
}
