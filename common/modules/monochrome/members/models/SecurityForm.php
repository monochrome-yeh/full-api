<?php

namespace common\modules\monochrome\members\models;

use Yii;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\LoginForm;

class SecurityForm extends LoginForm
{
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Members::t('app', 'Password'),
        ];
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Yii::$app->user->getIdentity();
        }

        return $this->_user;
    }

    public function security()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('common/app', 'Incorrect password.'));
            }
        }
    }
}
