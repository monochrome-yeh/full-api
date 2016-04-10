<?php
namespace backend\modules\monochrome\rbam\components;

use yii\web\Controller as baseController;
use Yii;

class Controller extends baseController
{

    public function init()
    {
        parent::init();
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $controller = $action->controller->id;
            if ($action->id !== 'first-change-password') {
                if (!Yii::$app->user->isGuest && !empty(Yii::$app->user->getIdentity()->vid) && Yii::$app->user->getIdentity()->passwd_changed == 0) {
                    return $this->redirect(["/first-change-password"]);
                }
            }
                
            return true;
        }

        return false;
    }
}
