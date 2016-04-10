<?php

namespace common\modules\monochrome\members\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for collection "cv_lenguage".
 *
 * @property \MongoId|string $_id
 * @property mixed $zh_tw
 * @property mixed $zh_cn
 * @property mixed $en
 */
class CVModel extends \yii\mongodb\ActiveRecord
{
    // public $name;
    // public $from;
    // public $tel;
    // public $age;
    // public $skills;
    // public $skill_details;
    // public $introduction;
    // public $portfolio;
    // public $experience;

    // /**
    //  * @inheritdoc
    //  */
    // public function behaviors()
    // {
    //     return [
    //         TimestampBehavior::className(),
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            'name',
            'email',
            'from',
            'tel',
            'age',
            'skills',
            'skill_details',
            'introduction',
            'portfolio',
            'experience',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'from', 'age', 'introduction', 'email'], 'required'],
            ['name', 'string', 'max' => 30, 'min' => 3],
            ['from', 'string', 'max' => 20,],
            ['email', 'email'],
            [['tel', 'skills', 'portfolio', 'experience'], 'safe', 'skipOnEmpty' => true],
            ['age', 'integer'],
            ['introduction', 'string', 'max' => 300],
            ['skill_details', 'string', 'max' => 3000],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function beforeValidate() {
        if (parent::beforeValidate()) {
            $tmp = ['tel', 'skills', 'portfolio', 'experience'];
            foreach ($tmp as $value) {
               $this->$value = array_filter((array)$this->$value, [$this, '_skipEmpty']);
            }

            return true;
        }    
    }

    private function _skipEmpty($value) {
        return !empty($value) || $value === 0;
    }

}
