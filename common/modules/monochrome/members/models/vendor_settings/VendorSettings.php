<?php

namespace common\modules\monochrome\members\models\vendor_settings;

use yii\base\Model;

class VendorSettings extends Model
{
    public $ui;
    public $cms;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['ui', 'embed_doc', 'embedArray' => false, 'model'=>'\common\modules\monochrome\members\models\vendor_settings\Ui'],
            ['cms', 'embed_doc', 'embedArray' => false, 'model'=>'\common\modules\monochrome\members\models\vendor_settings\VendorCmsSettings'],
        ];
    }
}
