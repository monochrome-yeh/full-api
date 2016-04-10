<?php

namespace common\modules\monochrome\members\models;

use Yii;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\LoginForm;

class VendorLoginForm extends LoginForm
{
	public $vid;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            // username and password are both required
            [['vid'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'vid' => Members::t('app', 'vid sn'),
        ]);
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = VendorUser::findOne([
            	'status' => self::STATUS_ACTIVE,
            	'login.vendor.vid' => $this->vid,
            	'login.vendor.account' => $this->username,
            ]);
        }

        return $this->_user;
    }

    protected function send_login_fail_to_much($user)
    {
        $ip = Yii::$app->request->getUserIp();
        $info = [];
        $info['userAgent'] = Yii::$app->request->getUserAgent();
        $info['userHost'] = Yii::$app->request->getUserHost();
        $admin = Vendor::getVendorAdmin($user->vid);
        $vendor = Vendor::getVendor($user->vid);

        Yii::$app->mailer->compose(Members::getEmailTemplate('vendor_user_login_too_much'), [
            'vendor_name' => $vendor->name,
            'ip' => $ip,
            'info' => $info,
        ])
        ->setFrom(Yii::$app->params['adminEmail'])
        ->setTo([$user->email, $admin->email])
        ->setSubject(Members::t('app', 'Login too much, please contact system admin.') . '（' . $vendor->name . Members::t('app', 'company') . Members::t('app', 'Logazine Agency System') . '）')
        ->send();
    }
}
