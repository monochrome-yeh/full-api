<?php

namespace common\modules\monochrome\games\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;

class GameModel extends ActiveRecord
{

    protected $_status = [];

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
        'id',
        'title',
        'tags',
        'createDate',
        'updateDate',
        'status',
        ];    
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //['login', 'require'],
            ['status', 'default', 'value' => $this->_status],
            ['status', 'is_array'],
        ];
    }

    public function is_array ($attribute, $params) {
        if (!is_array($this->attribute)) {
            $this->addError($attribute, '遊戲資料格式錯誤.');
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
}
