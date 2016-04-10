<?php

namespace common\components\monochrome\mitake\models;

use yii\base\Model;
use Yii;
/**
 * This is the model class for collection "project".
 *
 * @property \MongoId|string $_id
 * @property mixed $name
 */
class SmsModel extends Model
{

    public $body;
    public $phone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['body'], 'string', 'max' => '71'],
            ['phone', 'string', 'when' => function($model) {
                return !is_array($model->phone);
            }],
            ['phone', 'is_array', 'when' => function($model) {
                return !is_string($model->phone);
            }],
            ['phone', 'filter', 'filter' => function($attribute) {
                if (is_array($attribute)) {
                    $_attr = implode(';', $attribute);
                    preg_match('/[09]{2}[0-9]{8}/', $_attr, $result);
                    return implode(',', $result);
                }

                if (is_string($attribute)) {
                    preg_match('/^[09]{2}[0-9]{8}$/', $attribute, $result);
                    return implode(',', $result);
                }
            }],    
            ['body', 'filter', 'filter' => 'urlencode'],        
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'body' => Yii::t('common/app', 'body'),
            'phone' => Yii::t('common/app', 'phone'),              
        ];
    }  
  
}