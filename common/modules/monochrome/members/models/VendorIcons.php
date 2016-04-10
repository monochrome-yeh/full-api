<?php

namespace common\modules\monochrome\members\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * This is the model class for collection "project".
 *
 * @property \MongoId|string $_id
 * @property mixed $name
 */
class VendorIcons extends Model
{

    public $file1;
    public $file2;
    public $file3;
    public $file4;
    /**
     * @inheritdoc
     */


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'file1',
                'file2',
                'file3',
                'file4',
            ], 'file', 'extensions' => 'png, ico'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file' => Yii::t('common/app', 'File'),                      
        ];
    }     
}