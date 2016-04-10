<?php

namespace common\modules\monochrome\members\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use common\components\mark\TimeStandard;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\VendorUser;
use common\modules\monochrome\taxonomy\models\Type;

class Vendor extends ActiveRecord
{
    private static $_vendor_list;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'vendor';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimeStandard::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['active_date', 'expire_date'],
                    ActiveRecord::EVENT_BEFORE_INSERT => ['active_date', 'expire_date'],
                    ActiveRecord::EVENT_AFTER_FIND => [
                        'date' => ['expire_date', 'active_date'],
                    ],
                ],
            ],
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'alias',
            'maximum_members',
            'VAT',
            'user',
            'admin',
            'boss',
            'avatar',
            'business_owner',
            'manager',
            'sales',
            'module',
            'status',
            'settings',
            'active_date',
            'expire_date',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Members::t('app', 'Vendor Name'),
            'VAT' => Members::t('app', 'VAT'),
            'active_date' => Members::t('app', 'active date'),
            'expire_date' => Members::t('app', 'expire date'),
            'maximum_members' => Members::t('app', 'maximum members'),
            'status' => Yii::t('common/app', 'Active'),
            'settings' => Yii::t('common/app', 'Settings'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['avatar', 'string'],
            [['name', 'status'], 'required'],
            [['active_date', 'expire_date'], 'required'],
            [['name', 'alias'], 'unique'],
            ['status', 'boolean'],
            ['status', 'filter', 'filter' => 'intval'],
            ['alias', 'match', 'pattern' => '/^(?=.{3,16}$)(?=[a-zA-Z])(?![-])(?!.*[-]{2})[a-zA-Z0-9-]+(?<![-])$/', 'message' => 'format error'],
            ['VAT', 'integer', 'skipOnEmpty' => 1],
            [['maximum_members'], 'integer'],
            [['user', 'business_owner', 'module'], 'is_array'],
            [['active_date', 'expire_date'], 'date', 'format' => 'yyyy-MM-dd'],
            ['settings', 'embed_doc', 'embedArray' => false, 'model'=>'\common\modules\monochrome\members\models\vendor_settings\VendorSettings'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return (string)$this->getPrimaryKey();
    }

    public static function vendorAccess($vid)
    {
        return $vid == Yii::$app->user->getIdentity()->vid;
    }

    /**
     * @inheritdoc
     */
    public static function getVendorAdmin($vid)
    {
        $vendor = static::findOne(['_id' => $vid]);

        return VendorUser::findOne(['_id' => $vendor->admin]);
    }

    /**
     * @inheritdoc
     */
    public static function getVendor($vid)
    {
        $vendor = static::findOne(['_id' => $vid, 'status' => 1]);

        // if (empty($vendor)) {
        //     throw new NotFoundHttpException(Yii::t('app', 'Page not found.'));
        // }

        return $vendor;
    }

    public static function getVendorList()
    {
        if (self::$_vendor_list == null) {
            $result = [];

            foreach (static::find()->select(['name'])->asArray()->all() as $vendor) {
                $result[(string)$vendor['_id']] = $vendor['name'];
            }

            self::$_vendor_list = $result;
        }

        return self::$_vendor_list;
    }

    public function assign($uid)
    {
        $auth = Yii::$app->authManager;
        $vendorAdminRole = $auth->getRole(Yii::$app->getModule('members')->vendor_admin_role);
        $auth->assign($vendorAdminRole, $uid);

        return true;
    }

    private function addUser(VendorUser $user)
    {
        $users = ($this->user != null) ? $this->user : [];
        $this->user = array_unique(array_push($users, $user->getId()));
    }

    private function removeUser(VendorUser $user)
    {
        $users = (array)$this->user;
        if ($user->vid == $this->in_array($user-getId(), $users)) {
            unset($users[array_search($user-getId(), $users)]);
        }

        $this->user = $users;
    }

    public function addSales(VendorUser $user)
    {
        $users = ($this->sales != null) ? $this->sales : [];
        $this->sales = array_unique(array_push($users, $user->getId()));

        $this->adduser($user);

        return self;
    }

    public function removeSales(VendorUser $user)
    {
        $users = (array)$this->sales;
        if (in_array($user-getId(), $users)) {
            unset($users[array_search($user-getId(), $users)]);
        }

        $this->sales = $users;

        $this->removeUser($user);

        return self;
    }

    public function addManager(VendorUser $user)
    {
        $users = ($this->manager != null) ? $this->manager : [];
        $this->manager = array_unique(array_push($users, $user->getId()));

        $this->adduser($user);

        return self;
    }

    public function removeManager(VendorUser $user)
    {
        $users = (array)$this->manager;
        if ($this->in_array($user-getId(), $users)) {
            unset($users[array_search($user-getId(), $users)]);
        }

        $this->manager = $users;

        $this->removeUser($user);

        return self;
    }
}
