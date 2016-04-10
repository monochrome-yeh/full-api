<?php

namespace common\modules\monochrome\modelValidator\components;

use yii\validators\Validator;
use yii\validators\ValidationAsset;
use common\modules\monochrome\modelValidator\ModelValidator;

class IsArrayValidator extends Validator
{

    public $require = false;

    public function validateAttribute($model, $attribute)
    {
        $label = $model->getAttributeLabel($attribute);
        if ($this->validateValue($model->$attribute, $model, $attribute) === false) {
            $this->addError($model, $attribute, $this->getMessage($label));
        }
    }

    private function getMessage($attribute)
    {
        return ModelValidator::t('app', '{attribute} type error.', ['attribute' => $attribute]);
    }

    /**
     * Validates the given value.
     * @param mixed $value the value to be validated.
     * @return boolean whether the value is valid.
     */
    public function validateValue($value)
    {
        if (is_array($value)) {
            if ($this->require == true) {
                if (count($value == 0)) {
                    return false;
                }
            }
            return true;
            
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $label = $model->getAttributeLabel($attribute);
        $options = [
            'message' => \Yii::$app->getI18n()->format($this->getMessage($label), [
                'attribute' => $label,
            ], \Yii::$app->language),
        ];

        $message = json_encode($this->getMessage($label), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        ValidationAsset::register($view);
        return "
            if (!Array.isArray(value)) {
                messages.push({$message});
            }
        ";
    }
}
