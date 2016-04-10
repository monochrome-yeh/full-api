<?php

namespace backend\modules\monochrome\optimize\models;

use Yii;
use yii\base\Model;

/**
 * Chris form
 */
class Chris extends Model
{
    public $vendors;
    public $number;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vendors', 'number'], 'required'],
            ['number', 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'vendors' => 'Vendors',
            'number' => 'Number',
        ];
    }
}
