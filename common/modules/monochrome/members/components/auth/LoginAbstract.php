<?php
namespace common\modules\monochrome\members\components\auth;

abstract class LoginAbstract {
	abstract public function login($attributes = null);
}
