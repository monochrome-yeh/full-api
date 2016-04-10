<?php
namespace common\modules\monochrome\members\components;

use Yii;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\helpers\Url;
use yii\web\Cookie;

class SecurityFilter extends Behavior
{
    public $actions = [];
    /**
     * Declares event handlers for the [[owner]]'s events.
     * @return array events (array keys) and the corresponding event handler methods (array values).
     */
    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }
    /**
     * @param ActionEvent $event
     * @return boolean
     * @throws MethodNotAllowedHttpException when the request method is not allowed.
     */
    public function beforeAction($event)
    {
        //Yii::$app->securitySession->remove('userSecurityAuth');
        //echo Yii::$app->session->get('userSecurityAuth');exit;
        if (Yii::$app->getModule('members')->securityVerification === true && in_array($event->action->id, $this->actions)) {
            //var_dump($session->get('userSecurityAuth'));exit;
            $uid = Yii::$app->user->getId();

            $securityCookie = Yii::$app->getRequest()->getCookies()->getValue("{$uid}-security");
            if ($securityCookie == null) { 
                $url = urlencode(Url::current());
                //echo $url;exit;
                Controller::redirect(['/security', 'b' => $url]);
                //$this->redirect(['']);
                //$event->isValid = false;
                //throw new MethodNotAllowedHttpException('Method Not Allowed. This url can only handle the following request methods: ' . implode(', ', $allowed) . '.');
            }
        }
        return $event->isValid;
    }
}