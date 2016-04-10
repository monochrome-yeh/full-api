<?php

namespace common\components\monochrome\rbac;

use yii\rbac\Item as BaseItem;

class Item extends BaseItem {

    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;
    const TYPE_FIELD = 3;

    /**
     * @var string the item display_name
     */
    public $display_name;
    /**
     * @var string the item tag
     */
    public $tag;	
	
}
