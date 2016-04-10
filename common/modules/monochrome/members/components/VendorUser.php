<?php
namespace common\modules\monochrome\members\components;

use common\modules\monochrome\members\components\User;
use yii\web\Cookie;
use yii;

class VendorUser extends User
{

    public $vendorClass;

    public $vendorUrlTime = 604800;

    private $_vendor;

    protected function afterLogin($identity, $cookieBased, $duration)
    {
        parent::afterLogin($identity, $cookieBased, $duration);
        $loginUrl = $this->loginUrl;
        $loginUrl['vid'] = $identity->vid;
        if ($this->vendor == null && $identity->vid != null) {
        }

        $this->loginUrl = $loginUrl;

        //cookie
        $cookie = new Cookie([
            'name' => "{$this->idParam}-vendor",
            'value' => $this->loginUrl,
            'expire' => time() + (int)$this->vendorUrlTime,
        ]);
        Yii::$app->getResponse()->getCookies()->add($cookie);

        //cache
        // $cache = Yii::$app->other_cache->get("{$this->idParam}-vendor");
        // if ($cache === false) {
        //     Yii::$app->other_cache->set("{$this->idParam}-vendor", $this->loginUrl, time() + (int)$this->authTimeout);
        // }
    }

    protected function afterLogout($identity)
    {
        $uid = $identity->getId();
        $securityCookie = Yii::$app->getRequest()->getCookies()->getValue("{$uid}-security");
        if ($securityCookie != null) {
            Yii::$app->response->cookies->remove("{$uid}-security");
        }
        parent::afterLogout($identity);
    }

    public function loginRequired($checkAjax = true)
    {
        $request = Yii::$app->getRequest();
        if ($this->enableSession && (!$checkAjax || !$request->getIsAjax())) {
            if ($request->getUrl() == '/logout') {
                $this->setReturnUrl('/');
            } else {
                $this->setReturnUrl($request->getUrl());
            }
        }

        $vendorIdCookie = Yii::$app->getRequest()->getCookies()->getValue("{$this->idParam}-vendor");
        if ($vendorIdCookie !== null) {
            $loginUrl = (array)$vendorIdCookie;
            if ($loginUrl[0] !== Yii::$app->requestedRoute) {
                return Yii::$app->getResponse()->redirect($loginUrl[0].'/'.$loginUrl['vid']);
            }
        } else {
            //cache
            // $cache = Yii::$app->other_cache->get("{$this->idParam}-vendor");
            // if ($cache !== false) {
            //     $loginUrl = (array)$cache;
            //     if ($loginUrl[0] !== Yii::$app->requestedRoute) {
            //         return Yii::$app->getResponse()->redirect($loginUrl[0].'/'.$loginUrl['vid']);
            //     }
            // }
        }

        if ($this->loginUrl !== null) {
            $loginUrl = (array)$this->loginUrl;
            if ($loginUrl[0] !== Yii::$app->requestedRoute) {
                return Yii::$app->getResponse()->redirect($this->loginUrl);
            }
        }
        throw new ForbiddenHttpException(Yii::t('yii', 'Login Required'));
    }

    protected function renewAuthStatus()
    {
        parent::renewAuthStatus();
        $identity = $this->getIdentity();
        if ($identity != null && $identity->vid != null) {
            $vendorClass = $this->vendorClass;
            $this->_vendor = $vendorClass::findOne($identity->vid);
        }
    }

    public function getVendor()
    {
        return $this->_vendor;
    }
}
