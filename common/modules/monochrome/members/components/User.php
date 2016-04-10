<?php
namespace common\modules\monochrome\members\components;

use yii;
use yii\web\User as BaseUser;

class User extends BaseUser
{
	public $rememberMeTime = 604800; //3600*24*7

 	public function isSuperadmin()
 	{
 		if (!$this->getIsGuest() && $this->getIdentity()->role === Yii::$app->getModule('rbam')->superadmin_name) {
 			return true;
 		}

 		return false;
 	}

    public function isVendorAdmin() {
        if (!$this->getIsGuest() && $this->getIdentity()->role === Yii::$app->getModule('members')->vendor_admin_role) {
            return true;
        }

        return false;
    }

    public function isManager() {
        if (!$this->getIsGuest() && $this->getIdentity()->role === Yii::$app->getModule('members')->managerRoleName) {
            return true;
        }

        return false;
    }

    public function isBoss() {
        if (!$this->getIsGuest() && $this->getIdentity()->role === Yii::$app->getModule('members')->bossRoleName) {
            return true;
        }

        return false;
    }

    public function isAccountant() {
        if (!$this->getIsGuest() && $this->getIdentity()->role === Yii::$app->getModule('members')->accountantRoleName) {
            return true;
        }

        return false;
    }

    public function isSales() {
        if (!$this->getIsGuest() && $this->getIdentity()->role === Yii::$app->getModule('members')->salesRoleName) {
            return true;
        }

        return false;
    } 

    public function isOwner() {
        if (!$this->getIsGuest() && $this->getIdentity()->role === Yii::$app->getModule('members')->ownerRoleName) {
            return true;
        }

        return false;
    }                     	
}
