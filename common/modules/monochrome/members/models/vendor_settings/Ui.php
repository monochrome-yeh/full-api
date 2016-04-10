<?php

namespace common\modules\monochrome\members\models\vendor_settings;

use Yii;
use yii\base\Model;

class Ui extends Model
{

    public $theme;

    public $icons;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['theme', 'in', 'range' => array_values(Yii::$app->getModule('members')->theme_manager->getThemes())],
            ['icons', 'embed_doc', 'embedArray' => false, 'model'=>'\common\modules\monochrome\members\models\vendor_settings\Icons'],
        ];
    }
}
