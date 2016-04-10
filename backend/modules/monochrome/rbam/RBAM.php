<?php

namespace backend\modules\monochrome\rbam;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use backend\modules\monochrome\rbam\models\Assignment;
use common\modules\monochrome\members\models\AdminUser;
class RBAM extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\monochrome\rbam\controllers';

    public $defaultRoute = 'item';

    public $superadmin_name = 'superadmin';

    public $superadmin_email = 'logazine888@gmail.com';

    public function init()
    {
        parent::init();
        // custom initialization code goes here
        $this->createAdmin();
    }
    private function createAdmin () {
        $auth = Yii::$app->authManager;
        $superadmin_name = $this->superadmin_name;

        if (empty($auth->getRole($superadmin_name))) {
            $superadmin = $auth->createRole($superadmin_name);
            $auth->add($superadmin);
        }

        if (Assignment::find()->where(['item_name' => 'superadmin'])->count() === 0) {

            $user = new AdminUser(['scenario' => 'register']);
            $user->account = $this->superadmin_email;
            $user->role = $superadmin_name;
            if ($user->save() && $user->sendPasswordEmail()) {
                $superadmin = $auth->getRole($superadmin_name);
                $auth->assign($superadmin, $user->getId());
            }
            else {
                echo '<pre>';print_r($user->getErrors());
            }
        }

        $permissions = $auth->getPermissions();
        foreach ($permissions as $key => $value) {
            $auth->addChild($auth->getRole($superadmin_name), $value);
        }

    }
    protected function checkAccess()
    {
        $ip = Yii::$app->getRequest()->getUserIP();
        foreach (Yii::$app->getModule('members')->systemAdminAllowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        //Yii::warning('警告!!有非指定IP的人試圖登入系統管理員介面。 The requested IP is ' . $ip, __METHOD__);

        return false;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $controller = $action->controller->id;

        if (!parent::beforeAction($action)) {
            return false;
        }

        // if (Yii::$app instanceof \yii\web\Application && !$this->checkAccess()) {
        //     //throw new ForbiddenHttpException('You are not allowed to access this page.');
        //     return false;
        // }

        return true;
    }

    private static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/monochrome/rbam/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'zh-TW',
            'basePath' => '@backend/modules/monochrome/rbam/messages',
            'fileMap' => [
                'modules/monochrome/rbam/app' => 'app.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('modules/monochrome/rbam/' . $category, $message, $params, $language);
    }
}
