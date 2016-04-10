<?php

namespace common\modules\monochrome\modelValidator\components;

use yii\validators\Validator;
use yii\validators\ValidationAsset;
use common\modules\monochrome\modelValidator\ModelValidator;


class IsMongoIdValidator extends Validator
{

    public $require = false;

    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = ModelValidator::t('app', '{attribute} must be an array.');
        }
    }

    public function validateAttribute($model, $attribute)
    {
        if ($this->validateValue($model->$attribute) === false) {
            $this->addError($model, $attribute, $this->message);
        }
    }


    /**
     * Validates the given value.
     * @param mixed $value the value to be validated.
     * @return boolean whether the value is valid.
     */
    public function validateValue($value)
    {
        if (\MongoId::isValid($value)) {
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
            'message' => \Yii::$app->getI18n()->format($this->message, [
                'attribute' => $label,
            ], \Yii::$app->language),
        ];

        ValidationAsset::register($view);
        return 'yii.validation.is_mongoid(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }

}