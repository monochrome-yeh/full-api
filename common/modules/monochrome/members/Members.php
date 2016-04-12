<?php

namespace common\modules\monochrome\members;

use Yii;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use backend\modules\monochrome\rbam\models\Assignment;
use common\modules\monochrome\members\models\User;

class Members extends \yii\base\Module
{
    // Role Name
    public $managerRoleName = 'manager';
    public $salesRoleName = 'sales';
    public $bossRoleName = 'boss';
    public $ownerRoleName = 'owner';
    public $accountantRoleName = 'accountant';
    public $google = [
        'recaptcha' => [
            'enable' => false,
            'secret' => '',
        ]
    ];

    public function getDropDownListForCustomRoles()
    {
        $items = [];
        $customRoles = $this->custom_role;

        if (!empty($customRoles)) {
            $auth = Yii::$app->authManager;

            foreach ($customRoles as $customRole) {
                $items[$auth->getRole($customRole)->name] = static::t('app', $auth->getRole($customRole)->description);
            }
        }

        return $items;
    }

    public function getRoleName($role)
    {
        $auth = Yii::$app->authManager;
        if($auth->getRole($role) === null) {
            return static::t('app', 'Guest');
        }
        return static::t('app', $auth->getRole($role)->description);
    }    

    public $controllerNamespace = 'common\modules\monochrome\members\controllers';

    public $systemAdminAllowedIPs = ['127.0.0.1', '::1'];

    public $login_type = ['normal','facebook']; //['normal', 'facebook', 'vendor']

    public $login_fail = 0;

    public $securityUpdateExpair = 300;

    public $emailPassword = [];

    private $_emailPassword = [
        'enable' => true,
        'template' => [
            'register' => '@common/modules/monochrome/members/templates/register',
            'reset_password' => '@common/modules/monochrome/members/templates/reset_password',
            'vendorCreateUser' => '@common/modules/monochrome/members/templates/create-user',
        ]
    ];

    public $custom_role = [];

    public $vendor_admin_role = 'admin';

    public $vendor_worker_role = [];

    public $securityVerification = false;

    public function init()
    {
        parent::init();
        $this->createCustomRole();

        $this->theme_manager->initTheme();

        $vendor_limit = $this->getModule('vendor_limit');
        $vendor_limit::is_expire();
        $vendor_limit::is_active();
        // $this->controllerMap = [
        //   'vendoruser' => 'common\modules\monochrome\members\controllers\VendorUserController',
        //   // 'article' => [
        //   //    'class' => 'app\controllers\PostController',
        //   //    'pageTitle' => 'something new',
        //   // ],
        // ];
        // custom initialization code goes here
    }

    protected function checkAccess()
    {
        $ip = Yii::$app->getRequest()->getUserIP();
        foreach ($this->systemAdminAllowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        Yii::warning('警告!!有非指定IP的人試圖登入系統管理員介面。 The requested IP is ' . $ip, __METHOD__);

        return false;
    }

    private function createCustomRole()
    {
        $auth = Yii::$app->authManager;

        $customRoles = $this->custom_role;
        if (is_array($customRoles) && !empty($customRoles)) {
            if (is_string($this->vendor_admin_role) && !empty($this->vendor_admin_role) && empty($auth->getRole($this->vendor_admin_role))) {
                array_push($customRoles, $this->vendor_admin_role);
            }

            $customRoles = array_keys(array_flip($customRoles));
            foreach ($customRoles as $customRole) {
                if (is_string($customRole) && empty($auth->getRole($customRole))) {
                    $role = $auth->createRole($customRole);
                    $role->description = ucfirst(strtolower($customRole));
                    $auth->add($role);
                }
            }
        }
    }

    public function beforeAction($action)
    {
        $controller = $action->controller->id;

        if ($controller === 'vendor') {

            if (!parent::beforeAction($action)) {
                return false;
            }

            // if (Yii::$app instanceof \yii\web\Application && !$this->checkAccess()) {
            //     throw new ForbiddenHttpException('You are not allowed to access this page.');
            //     return false;
            // }
        }
        if ($controller === 'default' || $controller === 'vendor-user' || $controller === 'facebook') {
            if (!in_array($controller, $this->login_type)) {
                throw new NotFoundHttpException(Yii::t('app','Page not found.'));
            }
        }

        return true;
    }

    private static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/members/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'zh-TW',
            'basePath' => '@common/modules/monochrome/members/messages',
            'fileMap' => [
                'modules/monochrome/members/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('modules/monochrome/members/' . $category, $message, $params, $language);
    }

    public static function getEmailTemplate($string)
    {
        if (array_key_exists($string, Yii::$app->getModule('members')->getConfig()['template'])) {
            return Yii::$app->getModule('members')->getConfig()['template'][$string];
        }
    }

    private function getConfig()
    {
        return array_merge($this->_emailPassword, $this->emailPassword);
    }
}
