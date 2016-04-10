<?php

namespace common\modules\monochrome\taxonomy;

use Yii;
use common\modules\monochrome\taxonomy\models\Type;
use common\modules\monochrome\taxonomy\models\Item;
use yii\web\ServerErrorHttpException;

class Taxonomy extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\monochrome\taxonomy\controllers';

    public $edmAdMediaName = '行銷網頁(行動代銷秘書)';
    public $edmGuestStatusName = 'C 保持聯絡';

    private $_guest_status = [
        1 => 'A 預約回籠', 2 => 'B 有望客戶', 3 => 'C 保持聯絡', 

        11 => '覺得地點偏遠', 12 => '價格太貴', 13 => '和其他個案比較', 14 => '週邊環境太吵雜', 15 => '尚未完工（想買成屋）',
        16 => '戶數少不易管理', 17 => '投資客', 18 => '產品規劃不合', 19 => '想買純住家（零店面）', 20 => '自備款太高',
        21 => '無工程零付款', 22 => '要帶家人來看', 23 => '要問神明，地理師', 24 => '幫朋友來看', 25 => '拿資料（純參觀）',
        26 => '假客戶（疑市調）', 27 => '其他',

        100 => '已成交',
    ];

    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }

    private static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/taxonomy/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'zh-TW',
            'basePath' => '@common/modules/monochrome/taxonomy/messages',
            'fileMap' => [
                'modules/monochrome/taxonomy/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('modules/monochrome/taxonomy/' . $category, $message, $params, $language);
    }

    public function install_required()
    {
        $types = [];

        $types['ad_media'] = Type::find()->where(['unique_name' => 'ad_media'])->exists();

        $types['request_rooms'] = Type::find()->where(['unique_name' => 'request_rooms'])->exists();

        $types['request_square_meters'] = Type::find()->where(['unique_name' => 'request_square_meters'])->exists();

        $types['guest_status'] = Type::find()->where(['unique_name' => 'guest_status'])->exists();

        foreach ($types as $key => $value) {
            if (!$value) {
                $model = new Type();
                $model->unique_name = Yii::t('common/app' ,$key);
                $model->name = $key;

                if (!$model->save()) {
                    throw new ServerErrorHttpException(Yii::t('CMS content loaded error.'));
                }
            }
        }
    }

    // import content
    public function import_ad_media_edm_content($vid)
    {
        // import base content
        if (\MongoId::isValid($vid) && !Item::find()->where(['vid' => (string)$vid, 'name' => $this->edmAdMediaName])->exists()) {
            $model = new Item();
            $model->name = $this->edmAdMediaName;
            $model->type = "ad_media";
            $model->vid = (string)$vid;

            $model->save();
        }
    }

    // import content
    public function import_guest_status_content($vid)
    {
        // import base content
        if (\MongoId::isValid($vid) && !Item::find()->where(['vid' => (string)$vid, 'type' => 'guest_status'])->exists()) {
            foreach ($this->_guest_status as $key => $status) {
                $model = new Item();
                $model->name = $status;
                $model->type = "guest_status";
                $model->vid = (string)$vid;
                $model->spec_id = $key;

                $model->save();
            }
        }
    }
}
