<?php

namespace common\modules\monochrome\modelValidator\components;
 
use Yii;
use yii\validators\Validator;
use yii\validators\ValidationAsset;
use common\modules\monochrome\modelValidator\ModelValidator;

class EmbedDocValidator extends Validator
{
    public $scenario;
    public $model;
    public $embedArray = false;

    private static $_errors = [];

    public function init()
    {
        parent::init();

        if (!class_exists($this->model)) {
            die('embded document model class error.');
        }

        if ($this->message === null) {
            $this->message = ModelValidator::t('app', '{attribute} embded document has error.');
        }
    }

    /**
     * Validates a single attribute.
     * Child classes must implement this method to provide the actual validation logic.
     *
     * @param \yii\mongodb\ActiveRecord $object the data object to be validated
     * @param string $attribute the name of the attribute to be validated.
     */
    public function validateAttribute($object, $attribute)
    {
        $attr = $object->{$attribute};
        if (is_array($attr)) {
            if ($this->embedArray) {
                $data = [];
                foreach ($attr as $value) {
                    $result = $this->createModel($object, $attribute, $value);
                    if ($result) {
                        $data[] = $result;
                    }
                }
                $object->{$attribute} = $data;
            } else {
                $result = $this->createModel($object, $attribute, $attr);
                if ($result) {
                    $object->{$attribute} = array_merge($object->{$attribute}, $result);
                }
            }

        } else {
            $this->addError($object, $attribute, 'should be an array');
        }
    }

    /**
     * @inheritdoc
     */
    // public function clientValidateAttribute($model, $attribute, $view)
    // {
    //     $label = $model->getAttributeLabel($attribute);
    //     $options = [
    //         'message' => \Yii::$app->getI18n()->format($this->message, [
    //             'attribute' => $label,
    //         ], \Yii::$app->language),
    //     ];

    //     ValidationAsset::register($view);
    //     return 'yii.validation.embed_doc(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    // }

    private function createModel($object, $attribute, $attr)
    {
        $model = new $this->model;
        if ($this->scenario) {
            $model->scenario = $this->scenario;
        }

        // $model->attributes = $attr;
        // var_dump($model->load([\yii\helpers\StringHelper::basename(get_class($model)) => $attr]));print_r($model);exit;
        if ($model->load([\yii\helpers\StringHelper::basename(get_class($model)) => $attr]) && $model->validate()) {
            return $model->attributes;
        } else {
            foreach ($model->getErrors() as $errors) {
                $this->addError($object, $attribute, implode('', $errors));
            }

            return false;
        }
    }

    public static function getEmbedErrors()
    {
        $result = self::$_errors;
        self::$_errors = [];

        return $result;
    }
}
