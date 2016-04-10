<?php

namespace common\modules\monochrome\members\models;

use \Yii;
use yii\base\Model;
use yii\helpers\Url;
use common\modules\monochrome\members\models\User;
use common\modules\monochrome\members\models\Vendor;
use common\modules\monochrome\members\models\VendorUser;
use common\modules\monochrome\members\Members;

class RequestPasswordForm extends Model
{
    public $vid;
    public $account;
    public $email;

    private $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['vid', 'account', 'email'], 'required'],
            ['email', 'email'],
            [['account', 'email'], 'user_exists'],
        ];
    }

    public function user_exists($attribute, $params)
    {
        $this->_user = VendorUser::findOne([
                            'status' => VendorUser::STATUS_ACTIVE, 
                            'login.vendor.vid' => $this->vid, 
                            'login.vendor.account' => $this->account, 
                            'login.vendor.email' => $this->email
                        ]);

        if (empty($this->_user)) {
            $this->addError($attribute, Members::t('app', 'Incorrect account or email.'));
        }
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function requestPasswordReset()
    {
        $user = $this->_user;

        if (!empty($user)) {
            if (!$user->isPasswordResetTokenValid($user->password_reset_token)) {
                $vendor = Vendor::findOne($user->vid);
                if (!empty($vendor)) {
                    $user->generatePasswordResetToken();
                    if ($user->save()) {
                        $link = Url::toRoute(["/members/vendor-user/token/{$user->password_reset_token}"], true);

                        return Yii::$app->mailer->compose(Members::getEmailTemplate('reset_password'), [
                            'link' => $link,
                        ])
                        ->setFrom(Yii::$app->params['adminEmail'])
                        ->setTo($this->email)
                        ->setSubject(Members::t('app', 'Reset Password! {system_name}', ['system_name' => Yii::$app->name])) 
                        ->send();
                    }
                }
            } else {
                Yii::$app->session->setFlash('success', Members::t('app', 'You have already reset password recently.'));
            }
        }

        return false;
    }
}
