<?php

namespace common\modules\monochrome\modelValidator\components;

use yii\validators\Validator;
use yii\validators\ValidationAsset;
use common\modules\monochrome\modelValidator\ModelValidator;


class ArrayRequired extends Validator
{

    public $unsetOnEmpty = false;

    public function validateAttribute($model, $attribute)
    {
        $label = $model->getAttributeLabel($attribute);
        if ($this->validateValue($model->$attribute, $model, $attribute) === false) {
            $this->addError($model, $attribute, $this->getMessage($label));
        }
    }

    private function getMessage($attribute)
    {
        return ModelValidator::t('app', '{attribute} must has item.', ['attribute' => $attribute]);
    }
    /**
     * Validates the given value.
     * @param mixed $value the value to be validated.
     * @return boolean whether the value is valid.
     */
    public function validateValue($value, $model, $attribute)
    {
        if (!is_array($value) || (empty($value))) {
            return false;
        }
        else {
            $newArray = [];
            foreach ($value as $k => $v) {
                if ($v != null && $k != null) {
                    $newArray[$k] = $v;
                }
                else {
                    if (!$this->unsetOnEmpty) {
                        return false;
                    }
                }
            }

            if ($this->unsetOnEmpty) {
                if (!is_array($newArray) || (empty($newArray))) {
                    return false;
                }
                else{
                    $model->$attribute = $newArray;
                }
                
            }    
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    // public function clientValidateAttribute($model, $attribute, $view)
    // {
    //     $label = $model->getAttributeLabel($attribute);
    //     $options = [
    //         'message' => \Yii::$app->getI18n()->format($this->getMessage($label), [
    //             'attribute' => $label,
    //         ], \Yii::$app->language),
    //     ];

    //     $message = json_encode($this->getMessage($label), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    //     ValidationAsset::register($view);
    //     return "
    //         // var name = jQuery('form input[name^=\"Order[item_selector\"]');
    //         // console.log(jQuery(name).length);
    //         // if (parseInt(jQuery(name).length) < 2) {
    //         //     messages.push({$message});
    //         // }                
    //     ";
    // }

}