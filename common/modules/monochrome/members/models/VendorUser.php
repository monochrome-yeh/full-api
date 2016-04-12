<?php

namespace common\modules\monochrome\members\models;

use Yii;
use yii\helpers\Url;
use yii\behaviors\TimestampBehavior;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\Vendor;
use frontend\modules\project_management\monochrome\project\models\Project;

class VendorUser extends User
{
    private static $_vendorUsersByVendor;

    const STATUS_VENDOR_DELETED = -2;

    public $vid;

    protected $_type = 'vendor';

    protected $sendEmail = false;

    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->passwd_changed = 0;
        }
    } 

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            // 'role',
            'pid',
            'experience_pid',
            'heir',
            'passwd_changed',
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            // 'role' => Members::t('app', 'Role'),
            'pid' => Members::t('app', 'Project'),
            'heir' => Members::t('app', 'Heir'),
        ]);
    }

    public function rules()
    {
        return [
            [['vid', 'email', 'account'], 'required'],
            ['phone', 'check_phone'],
            ['email', 'email'],
            ['email', 'unique_email', 'on' => ['admin_register', 'register', 'update', 'profile']],
            ['account', 'unique_account', 'on' => ['admin_register', 'register', 'update', 'profile']],
            ['account', 'match',
                'pattern' => "/^(?=.{3,20}$)(?=[a-zA-Z])(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?![_.])$/",
                'message' => Members::t('app', '{min} {max} Account format error.', ['min' => 3, 'max' => 20]),
            ],
            [['tags', 'password'], 'trim'],
            [['pid', 'experience_pid'], 'check_project'],
            ['username', 'string', 'max' => 20, 'min' => 2],
            ['password', 'string', 'max' => 20, 'min' => 8, 'on' => ['admin_register', 'register', 'profile']],
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
            ['role', 'in', 'range' => Yii::$app->getModule('members')->custom_role, 'on' => ['register', 'update']],
            ['role', 'in', 'range' => (array)Yii::$app->getModule('members')->vendor_admin_role, 'on' => ['admin_register']],
            ['activity', 'boolean', 'on' => 'update'],
            ['heir', 'inherit_check', 'on' => ['update']],
            ['passwd_changed', 'filter', 'filter' => 'intval'],
        ];
    }

    public function inherit_check($attribute, $params)
    {
        if ($this->$attribute != null && static::find()->where(['_id' => $this->$attribute, 'status' => self::STATUS_ACTIVE, '_id' => ['$ne' => $this->getId()]])->exists()) {
            if ($this->activity == true) {
                $this->addError($attribute, Members::t('app', 'Active user cant inherit Data to anyone, and heir need an active user'));
            }
        }
    }

    private function getAllProjects()
    {
        $projects = Project::getListByVendor($this->vid);
        return array_keys(array_merge($projects['active'], $projects['unActive']));
    }

    public function check_project($attribute, $params)
    {
        $projects = $this->getAllProjects();

        foreach ($this->$attribute as $project) {
            if (!in_array($project, $projects)) {
                $this->addError($attribute, Members::t('app', 'Wrong Project'));
            }
        }
    }

    public static function is_heir($user, $heir)
    {
        return static::find()->where(['_id' => $user, 'heir' => $heir])->exists();
    }

    public static function getInheirtByHeir($heir)
    {
        return array_map(function($item) {
            return (string)$item;
        }, Yii::$app->mongodb->getCollection(static::collectionName())->distinct('_id', ['heir' => $heir]));
    }

    public static function getHeirByUser($uid)
    {
        $user = static::find()->where(['_id' => $uid])->asArray()->select(['heir'])->One();
        if ($user != null && !empty($user['heir'])) {
            return static::findOne($user['heir']);
        }

        return null;
    }

    public function unique_email($attribute, $params)
    {
        $model = Yii::$app->mongodb->getCollection($this->collectionName());

        $count = $model->find([
            '$and' => [
                ['login.vendor.vid' => $this->vid],
                ['login.vendor.email' => $this->email],
                ['_id' => ['$ne' => $this->_id]],
            ]
        ])->count();

        if ($count > 0) {
            $this->addError($attribute, Members::t('app', 'Someone already has that email. Try another?'));
        }
    }

    public function unique_account($attribute, $params)
    {
        $model = Yii::$app->mongodb->getCollection($this->collectionName());

        $count = $model->find([
            '$and' => [
                ['login.vendor.vid' => $this->vid],
                ['login.vendor.account' => $this->account],
                ['_id' => ['$ne' => $this->_id]],
            ]
        ])->count();

        if ($count > 0) {
            $this->addError($attribute, Members::t('app', 'Someone already has that account. Try another?'));
        }
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $login = $this->login;

        $login['vendor']['password'] = Yii::$app->security->generatePasswordHash($password);

        $this->login = $login;
    }

    public function afterFind()
    {
        $this->vid = $this->login['vendor']['vid'];
        $this->email = $this->login['vendor']['email'];
        $this->account = $this->login['vendor']['account'];
        $this->password = $this->login['vendor']['password'];

        // Getting vendor user role from the authManager - start 
        $auth = Yii::$app->authManager;
        $vendorUserRoleAssignments = $auth->getAssignments($this->getId());

        if (!empty($vendorUserRoleAssignments)) {
            foreach ($vendorUserRoleAssignments as $vendorUserRole) {
                $this->role = $vendorUserRole->roleName;
            }
        }
        // Getting vendor user role from the authManager - end 

        return true;
    }

    public function beforeSave($insert)
    {
        $login = $this->login;
        $login['vendor']['vid'] = $this->vid;
        $login['vendor']['email'] = $this->email;
        $login['vendor']['account'] = $this->account;
        $this->login = $login;

        if (parent::beforeSave($insert)) {
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
                if ($this->activity) {
                    $this->heir = null;
                }
            }

            return true;
        }
        
        return false;    
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->scenario === 'register' || $this->scenario === 'update' || $this->scenario === 'admin_register') {
            // Adding vendor user role - start
            $auth = Yii::$app->authManager;

            $auth->revokeAll($this->getId());

            $vendorUserRole = $auth->getRole($this->role);
            if (!empty($vendorUserRole)) {
                $auth->assign($vendorUserRole, $this->getId());
            }
            // Adding vendor user role - end
        }

        return true;
    }

    public function delete($delete_by_superadmin = false)
    {
        $this->status = self::STATUS_VENDOR_DELETED;
        if ($delete_by_superadmin === true) {
            $this->status = self::STATUS_DELETED;
        }

        if ($this->save(true, ['status'])) {
            Yii::$app->mongodb->getCollection('vendor')->update(['_id' => $this->vid], [
                '$pull' => [
                    'user' => $this->getId()
                ]
            ]);

            $auth = Yii::$app->authManager;
            $auth->revokeAll($this->getId());

            return true;
        }

        return false;
    }  

    public function reset()
    {
        $this->generateAuthKey();
        $this->password = substr(uniqid(), -8);
        $this->setPassword($this->password);
        // $this->login_fail = 0;

        if ($this->save()) {
            $this->sendEmail = true;
            return true;
        }

        return false;
    }

    public function sendAdminPasswordEmail($vendorModel)
    {
        if ($this->sendEmail === true && $vendorModel != null) {
            $link = Yii::$app->urlManagerFrontend->createAbsoluteUrl(["/login/{$this->vid}"], true);

            return Yii::$app->mailer->compose('@common/modules/monochrome/members/templates/vendor_admin_register', [
                'link' => $link,
                'account' => $this->account,
                'vendor' => $vendorModel->name,
                // 'active' => date('Y-m-d', $vendorModel->active_date),
                // 'expire' => date('Y-m-d', $vendorModel->expire_date),
                'password' => $this->password,
            ])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject(Members::t('app', 'Admin Registration Info {system_name}', ['system_name' => Yii::$app->name ]))
            ->send();
        }

        return true;
    }

    public function sendPasswordEmail($vendorModel = null)
    {
        if ($this->sendEmail === true && $vendorModel != null) {
            $link = Yii::$app->urlManagerFrontend->createAbsoluteUrl(["/login/{$this->vid}"], true);

            return Yii::$app->mailer->compose('@common/modules/monochrome/members/templates/vendor_register', [
                'link' => $link,
                'account' => $this->account,
                'vendor' => $vendorModel->name,
                'password' => $this->password,
            ])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject(Members::t('app', 'Registration Info {system_name}', ['system_name' => Yii::$app->name ]))
            ->send();
        }

        return true;
    }

    public function sendResetPasswordEmail($vendorModel)
    {
        if ($this->sendEmail === true && $vendorModel != null) {
            $link = Yii::$app->urlManagerFrontend->createAbsoluteUrl(["/login/{$this->vid}"], true);

            return Yii::$app->mailer->compose('@common/modules/monochrome/members/templates/vendor_user_rest_password', [
                'link' => $link,
                'account' => $this->account,
                'vendor' => $vendorModel->name,
                'password' => $this->password,
            ])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject(Members::t('app', 'Reset Password Info {system_name}', ['system_name' => Yii::$app->name ]))
            ->send();
        }

        return true;
    }

    public function getUserStatus()
    {
        return parent::getUserStatus() + [
            self::STATUS_VENDOR_DELETED => Members::t('app', 'Vendor Delete'),
        ];
    }

    public function sendEnableEmail()
    {
        if ($this->sendEmail === true) {
            if ($this->vid != null) {
                $vid = $this->vid;
            }
            else {
                $vid = Yii::$app->user->getIdentity()->vid;
            }
            
            $link = Yii::$app->urlManagerFrontend->createAbsoluteUrl(["/login/$vid"], true);

            $vendor = Vendor::findOne($vid);

            if (!empty($vendor)) {
                $subject = Members::t('app', 'Account Enable!') . '（' . $vendor->name . Members::t('app', 'company') . Members::t('app', 'Logazine Agency System') . '）';

                return Yii::$app->mailer->compose(Members::getEmailTemplate('notification_enable'), [
                    'account' => $this->account,
                    'link' => $link,
                ])
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo($this->email)
                ->setSubject($subject)
                ->send();
            }
        }

        return true;
    }

    public function sendDisableEmail()
    {
        if ($this->sendEmail === true) {
            $vendor = Vendor::findOne(Yii::$app->user->getIdentity()->vid);

            if (!empty($vendor)) {
                $subject = Members::t('app', 'Account Disable!') . '（' . $vendor->name . Members::t('app', 'company') . Members::t('app', 'Logazine Agency System') . '）';

                return Yii::$app->mailer->compose(Members::getEmailTemplate('notification_disable'), [
                    'account' => $this->account,
                ])
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo($this->email)
                ->setSubject($subject)
                ->send();
            }
        }

        return true;
    }

    public static function getVendorId($uid)
    {
        $vendorUser = static::findOne(['_id' => $uid]);

        if (!empty($vendorUser)) {
            return $vendorUser['login']['vendor']['vid'];
        }

        return false;
    }

    public static function getWorkerListByVendor($vid = null)
    {
        $array = static::find()->where([
            'login.vendor.vid' => $vid === null ? Yii::$app->user->getIdentity()->vid : $vid,
            'role' => ['$in' => Yii::$app->getModule('members')->vendor_worker_role],
            'status' => self::STATUS_ACTIVE,
        ])
        ->select(['_id', 'username'])
        ->asArray()
        ->all();

        $result = [];

        foreach ((array)$array as $value) {
            $result[(string)$value['_id']] = $value['username'];
        }

        return $result;
    }

    public static function getWorkerListByProject($pid)
    {
        $array = static::find()->where([
            'pid' => ['$in' => (array)$pid],
            'role' => ['$in' => Yii::$app->getModule('members')->vendor_worker_role],
            'status' => self::STATUS_ACTIVE,
        ])
        ->select(['_id', 'username'])
        ->asArray()
        ->all();

        $result = [];

        foreach ((array)$array as $value) {
            $result[(string)$value['_id']] = $value['username'];
        }

        return $result;
    }

    public static function getUserNameById($id)
    {
        $model = static::find()->where(['_id' => (string)$id])->select(['username', '_id' => false])->asArray()->one();

        if ($model != null) {
             return $model['username'];
        }

        return '';
    }

    public static function getManagersByProject($pid, $name = false)
    {
        $result = [];

        $managers = static::find()->where(['pid' => $pid, 'role' => Yii::$app->getModule('members')->managerRoleName])->select(['_id', 'username'])->all();
        if (!empty($managers)) {
            foreach ($managers as $key => $value) {
                if ($name) {
                    $result[(string)$value->_id] = $value->username;
                } else {
                    $result[] = (string)$value->_id;
                }
            }
        }

        return $result;
    }

    public static function getSalesByProject($pid)
    {
        $result = [];

        $sales = static::find()->where(['pid' => $pid, 'role' => Yii::$app->getModule('members')->salesRoleName])->select(['_id'])->all();
        if (!empty($sales)) {
            foreach ($sales as $key => $value) {
                $result[] = (string)$value->_id;
            }
        }

        return $result;
    }

    public static function getVendorUserProjects($pids, $vid = null)
    {
        if ($vid == null) {
            $vid = Yii::$app->user->identity->vid;
        }

        $projects = Project::getListByVendor($vid);
        $projects = array_merge($projects['active'], $projects['unActive']);

        return array_intersect_key($projects, array_flip((array)$pids));
    }

    public static function getVendorUsers($uids)
    {
        $ids = $result = [];
        $uids = (array)$uids;
        foreach ($uids as $uid) {
            $ids[] = new \MongoId($uid);
        }

        foreach (static::find()->where(['_id' => ['$in' => $ids]])->select(['username'])->asArray()->all() as $vendorUser) {
            $result[(string)$vendorUser['_id']] = $vendorUser['username'];
        }

        return $result;
    }

    public static function getVendorUsersByVendor($vid = null)
    {
        if (self::$_vendorUsersByVendor == null) {
            $result = [];

            foreach (static::find()->where(['login.vendor.vid' => $vid == null ? Yii::$app->user->getIdentity()->vid : $vid])->select(['_id', 'username'])->asArray()->all() as $vendorUser) {
                $result[(string)$vendorUser['_id']] = $vendorUser['username'];
            }

            self::$_vendorUsersByVendor = $result;
        }

        return self::$_vendorUsersByVendor;
    }
}
