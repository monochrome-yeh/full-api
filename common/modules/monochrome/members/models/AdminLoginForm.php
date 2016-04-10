<?php

namespace common\modules\monochrome\members\models;

class AdminLoginForm extends LoginForm
{
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = AdminUser::findOne([
                'status' => self::STATUS_ACTIVE,
                'login.normal.account' => $this->username
            ]);
        }

        return $this->_user;
    }
}
