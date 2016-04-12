<?php

namespace common\modules\monochrome\members\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;
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
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = -1;
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 10;

    public $email;

    public $account;

    public $password;

    public $password_repeat;

    protected $sendEmail = false;

    protected $_type = 'normal';

    public $activity;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'cv',
            //'pid',
            'avatar',
            'login',
            'role',
            'detail',
            'username',
            'settings',
            'tags',
            'agree_terms',
            'status',
            'login_fail',
            'password_reset_token',
            'created_at',
            'updated_at',
            'auth_key',
            'phone',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            //'pid' => Members::t('app', 'Project'),
            'account' => Members::t('app', 'Account'),
            'username' => Members::t('app', 'Username'),
            'status' => Members::t('app', 'Status'),
            'settings' => Yii::t('common/app', 'Settings'),
            'role' => Members::t('app', 'Role'),
            'password' => Members::t('app', 'Password'),
            'password_repeat' => Members::t('app', 'Password Confirmation'),     
            'phone' => Members::t('app', 'Phone'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password_reset_token', 'cv'], 'safe'],
            ['phone', 'check_phone'],
            ['account', 'required'],
            ['account', 'email'],
            ['account', 'unique_account', 'on' => ['register', 'update', 'profile']],
            // ['account', 'match',
            //     'pattern' => '/^(?=.{5,18}$)(?=[a-zA-Z])(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/',
            //     'message' => Members::t('app', 'Account format error.'),
            // ],
            ['avatar', 'string'],
            [['tags'], 'is_array'],
            [['tags', 'password'], 'trim'],
            ['username', 'string', 'max' => 20, 'min' => 2],
            ['password', 'string', 'max' => 20, 'min' => 8, 'on' => ['register', 'profile']],
            ['settings', 'embed_doc', 'embedArray' => false, 'model'=>'\common\modules\monochrome\members\models\user_settings\Settings'],
            // ['password_repeat', 'required',
            //     'when' => function($model) {
            //         if (!empty($this->password) && empty($this->password_repeat)) {
            //             return true;
            //         }
            //         return false;
            //     },
            //     'whenClient' => "function (attribute, value) {
            //         if ($('#vendoruser-password').val().length > 0 && $('#vendoruser-password_repeat').val().length === 0) {
            //             return true;
            //         }
            //         return false;
            //     }",
            // 'on' => 'profile'],
            ['password', 'compare', 'message' => Members::t('app', 'Confirmation password is not equals to password'), 'on' => 'profile', 'skipOnEmpty' => 1],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => Members::t('app', 'Confirmation password is not equals to password'), 'on' => 'profile', 'skipOnEmpty' => 1],
            ['login_fail', 'default' , 'value' => 0],
            ['status', 'filter', 'filter' => function($value) {
                if ($this->isNewRecord) {
                    return self::STATUS_ACTIVE;
                } else {
                    return intval($value);
                }
            }],
            ['status', 'in', 'range' => array_keys($this->getUserStatus())],
            ['activity', 'boolean', 'on' => 'update'],
        ];
    }

    public function check_phone($attribute, $params)
    {
        $phone = $this->$attribute;

        if (!empty($phone) && !preg_match('/^[0-9]+#?-?[0-9]+$/', $phone)) {
            $this->addError($attribute, Members::t('app', 'Wrong Phone.'));
        }
    }

    public function unique_account($attribute, $params)
    {
        $model = Yii::$app->mongodb->getCollection($this->collectionName());

        $count = $model->find([
            '$and' => [
                ['login.normal.account' => $this->account],
                ['_id' => ['$ne' => $this->_id]],
            ]
        ])->count();

        if ($count > 0) {
            $this->addError($attribute, Members::t('app', 'Someone already has that account. Try another?'));
        }
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);

        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return (string)$this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $login = $this->login;

        $login['normal']['password'] = Yii::$app->security->generatePasswordHash($password);

        $this->login = $login;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function afterFind()
    {
        parent::afterFind();

        if (array_key_exists('normal', $this->login)) {
            $this->account = $this->login['normal']['account'];
            $this->password = $this->login['normal']['password'];

            $this->email = $this->account;
        }

        return true;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            
            if ($this->_type === 'normal') {
                $login = $this->login;
                $login['normal']['account'] = $this->account;
                $this->login = $login;

                if (empty($this->username)) {
                    $this->username = $this->account;
                }

                if ($this->isNewRecord) {
                    $this->generateAuthKey();

                    $this->password = substr(uniqid(), -8);
                    $this->setPassword($this->password);

                    $this->sendEmail = true;
                } elseif (!empty($this->password) && $this->scenario === 'profile') {
                    $this->setPassword($this->password);
                } elseif ($this->scenario === 'update') {
                    $this->status = $this->activity ? self::STATUS_ACTIVE : self::STATUS_UNACTIVE;
                }
            }

            return true;            
        }    

        return false;

    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        return true;
    }

    public function delete()
    {
        $this->status = self::STATUS_DELETED;

        if ($this->save()) {
            return true;
        }

        return false;
    }

    public function reset()
    {
        $this->generateAuthKey();
        $this->password = substr(uniqid(), -8);
        $this->setPassword($this->password);

        if ($this->save()) {
            $this->sendEmail = true;
            return true;
        }

        return false;
    }

    public function enable()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->login_fail = 0;

        if ($this->save()) {
            $this->sendEmail = true;
            return true;
        }

        return false;
    }

    public function disable()
    {
        $this->status = self::STATUS_UNACTIVE;

        if ($this->save()) {
            $this->sendEmail = true;
            return true;
        }

        return false;
    }

    public function sendPasswordEmail($userModel = null)
    {
        if ($this->sendEmail === true) {
            $link = Url::toRoute(["/login"], true);

            return Yii::$app->mailer->compose(Members::getEmailTemplate('register'), [
                'link' => $link,
                'account' => $this->account,
                'password' => $this->password,
            ])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->account)
            ->setSubject('Your Register!')
            ->send();
        }

        return true;
    }

    public function getUserStatus()
    {
        return [
            self::STATUS_DELETED => Members::t('app', 'Delete'),
            self::STATUS_UNACTIVE => Members::t('app', 'Disable'),
            self::STATUS_ACTIVE => Members::t('app', 'Enable'),
        ];
    }

    public static function getUserStatusAction($id, $status, $icon = true)
    {
        $result = [
            self::STATUS_UNACTIVE => 
                Html::a($icon ? '<span class="glyphicon glyphicon-ok"></span>' : Yii::t('common/app', 'Enable User'), Url::toRoute(['vendor-user/enable', 'id' => $id]), [
                    'class' => 'btn btn-success',
                    'title' => Yii::t('common/app', 'Enable User'),
                    'data-confirm' => Yii::t('common/app', 'Are you sure you want to enable this user?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]),
            self::STATUS_ACTIVE => 
                Html::a($icon ? '<span class="glyphicon glyphicon-remove"></span>' : Yii::t('common/app', 'Disable User'), Url::toRoute(['vendor-user/disable', 'id' => $id]), [
                    'class' => 'btn btn-warning',
                    'title' => Yii::t('common/app', 'Disable User'),
                    'data-confirm' => Yii::t('common/app', 'Are you sure you want to disable this user?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]),
        ];

        if (!isset($result[$status])) {
            return null;
        }
        return $result[$status];
    }

    public function getAvatar(){
        if (!empty($this->avatar)) {
            return Yii::$app->s3->createUrl($this->avatar);
        }

        return '';
    }
}
