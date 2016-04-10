<?php

namespace common\modules\monochrome\userReport\models;

use Yii;
use common\modules\monochrome\userReport\UserReport as UserReportModule;
/**
 * This is the model class for collection "user_report".
 *
 * @property \MongoId|string $_id
 * @property mixed $uid
 * @property mixed $desc
 * @property mixed $type
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class UserReport extends \yii\mongodb\ActiveRecord
{

    const TYPE_UI_ERROR = 1;
    const TYPE_UX_ERROR = 2;
    const TYPE_FUNCTION_ERROR = 3;
    const TYPE_SUGGESTION = 11;

    public function init() {
        parent::init();
        if ($this->isNewRecord) {
            $this->is_solved = 0;
        }
    }

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'user_report';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'uid',
            'desc',
            'type',
            'is_solved',
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
            ['desc', 'string', 'max' => 100],
            ['is_solved', 'boolean'],
            ['is_solved', 'filter', 'filter' => 'intval'],
            [['uid', 'desc', 'type', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', 'Uid'),
            'desc' => Yii::t('common/app', 'Desc'),
            'type' => Yii::t('common/app', 'Type'),
            'is_solved' => Yii::t('common/app', 'Is Solved'),
            'created_at' => Yii::t('common/app', 'Created At'),
            'updated_at' => Yii::t('common/app', 'Updated At'),
        ];
    }

    public static function getTypeList() {
        return [
            self::TYPE_UI_ERROR => UserReportModule::t('app', 'TYPE_UI_ERROR'),
            self::TYPE_UX_ERROR => UserReportModule::t('app', 'TYPE_UX_ERROR'),
            self::TYPE_FUNCTION_ERROR => UserReportModule::t('app', 'TYPE_FUNCTION_ERROR'),
            self::TYPE_SUGGESTION => UserReportModule::t('app', 'TYPE_SUGGESTION'),
        ];
    }
}
