<?php

namespace common\modules\monochrome\members\models\vendor_settings;

use yii\base\Model;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\taxonomy\models\Type;

class VendorCmsSettings extends Model
{
    public $list;
    public $multi;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['list', 'multi'], 'check_cms_type'],
            [['list', 'multi'], 'is_array'],
        ];
    }

    public function check_cms_type($attribute, $params)
    {
        $types = array_keys(Type::getTypeList());

        foreach ($this->$attribute as $type) {
            if (!in_array($type, $types)) {
                $this->addError($attribute, Members::t('app', 'Wrong Cms Type'));
            }
        }
    }    
}
