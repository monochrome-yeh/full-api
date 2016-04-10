<?php

namespace common\modules\monochrome\members\models\user_settings;

use yii\base\Model;
use Yii;

class Settings extends Model
{

    public $font_size;
    public $avatar;

    const FONT_SIZE_SMALL = 'small';
    const FONT_SIZE_MEDIUM = 'medium';
    const FONT_SIZE_LARGE = 'large';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['font_size', 'in', 'range' => array_keys(static::getSizeList())],
            ['avatar', 'file', 'extensions' => 'png, jpg, gif', 'maxSize' => 1024*100],
        ];
    }

    public static function getSizeList(){
        return [
            'small' => Yii::t('common/app', 'Font Size Small'),
            'medium' => Yii::t('common/app', 'Font Size Medium'),
            'large' => Yii::t('common/app', 'Font Size Large'),
        ];
    }
}
