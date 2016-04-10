<?php

namespace common\modules\monochrome\members\models\vendor_settings;

use Yii;
use yii\base\Model;

class Icons extends Model
{

    public $s_57x57;
    public $s_72x72;
    public $s_114x114;
    public $s_144x144;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    's_57x57',
                    's_72x72',
                    's_114x114',
                    's_144x144',
                ],
                'string'    
            ]
        ];
    }
}
