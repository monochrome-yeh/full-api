<?php

namespace backend\modules\monochrome\topUp\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use backend\modules\monochrome\topUp\TopUp;

/**
 * This is the model class for collection "project_top_up_log".
 *
 * @property \MongoId|string $_id
 * @property mixed $vid
 * @property mixed $pid
 * @property mixed $creator
 * @property mixed $log_content
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class ProjectTopUpLog extends ActiveRecord
{
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'project_top_up_log';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'vid',
            'pid',
            'creator',
            'log_content',
            'created_at',
            'updated_at',
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['log_content', 'trim'],
            ['creator', 'filter', 'filter' => function($value) { return Yii::$app->user->getId(); }],
            [['vid', 'pid', 'creator', 'log_content'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => TopUp::t('app', 'ID'),
            'vid' => TopUp::t('app', 'Vendor'),
            'pid' => TopUp::t('app', 'Project'),
            'creator' => TopUp::t('app', 'Creator'),
            'log_content' => TopUp::t('app', 'Log Content'),
            'created_at' => Yii::t('common/app', 'Created At'),
            'updated_at' => Yii::t('common/app', 'Updated At'),
        ];
    }
}
