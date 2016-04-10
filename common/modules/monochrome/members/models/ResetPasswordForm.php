<?php

namespace common\modules\monochrome\members\models;

use \Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\helpers\Html;
use common\modules\monochrome\members\models\User;
use common\modules\monochrome\members\Members;

class ResetPasswordForm extends Model
{
    public $password;
    public $password_repeat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            ['password', 'string', 'max' => 12, 'min' => 8],
            ['password', 'compare', 'compareAttribute' => 'password_repeat', 'message' => Members::t('app', 'Password is not equals to confirmation password')],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => Members::t('app', 'Confirmation password is not equals to password')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Members::t('app', 'Password'),
            'password_repeat' => Members::t('app', 'Password Confirmation'),
        ];
    }

    public function resetPassword(User $user)
    {
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        $user->passwd_changed = 1;
        
        return $user->save();
    }
}
