<?php

namespace common\modules\monochrome\members\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use common\modules\monochrome\members\Members;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class FacebookUser extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['login', 'required'],
            ['login', 'unique_account', 'on' => 'register'],
            [['tags'], 'is_array'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => $this->getUserStatus()],
        ];
    }

    public function unique_account ($model, $attribute)
    {
        $account = $this->login['facebook']['account'];

        if ($this->validateEmail($account)) {
            $this->addError($attribute, Members::t('app', 'Account must be an e-mail.'));
        }

        $count = static::find()->where([
            'login.facebook.account' => $account,
        ])->count();

        if ($count > 0) {
            $this->addError($attribute, Members::t('app', 'Someone already has that username. Try another?'));
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->generateAuthKey();
            }

            return true;
        }

        return false;
    }

    public function afterFind()
    {
        return;
    }
}
