<?php

namespace backend\modules\monochrome\rbam\components;

use yii\filters\AccessControl as BaseAccessControl;
use Yii;
use backend\modules\monochrome\rbam\models\Item;
use backend\modules\monochrome\rbam\models\Assignment;


class AccessControl extends BaseAccessControl {
	
	public $autoRules = [];

    public function beforeAction($action) {

    	$this->autoCreateRule($action);
        return parent::beforeAction($action);  	
    }

	public function autoCreateRule($action) {
		$cache_name = "{$action->controller->module->id}_{$action->controller->id}";
        $itemsModel = Item::find()->where(['_id' => new \MongoRegex("/^{$cache_name}_/")])
        ->andWhere(['_id' =>['$not' => new \MongoRegex("/#/")]])
        ->select(['_id'])->asArray()->all();
            
        $items = [];

        foreach ($itemsModel as $value) {
            $items[] = $value['_id'];
        }

		$permission_name = 'permission_list';
		$result = [];
		$cache = [];
    	if (Yii::$app->cache->get($cache_name) == false) {

	        Yii::$app->cache->set($cache_name, $this->cacheProcess($action), 1800);
	        $cache = Yii::$app->cache->get($cache_name);
		}
		else {
			$_result = is_array($this->getAutoRuleActions($action)) ? $this->getAutoRuleActions($action) : [];
			$cache = Yii::$app->cache->get($cache_name);

			if (!empty(array_diff($_result, $cache['result']))) {
				Yii::$app->cache->set($cache_name, $this->cacheProcess($action), 1800);
			}
		}
		
		$auth = Yii::$app->authManager;
		$result = $cache['result'];
		$this->rules = $cache['rules'];

    	if (Yii::$app->cache->get($permission_name) == false) {
    		Yii::$app->cache->set($permission_name, array_keys($auth->getPermissions()), 1800);
    	}

    	$permissions = Yii::$app->cache->get($permission_name);
    	$add = array_diff($result, $permissions);
    	$remove = array_diff($items, $result);

    	//add
    	foreach ($add as $key => $value) {
    		$permission = $auth->createPermission($value);
    		$permission->tag = $cache_name;
    		$permission->display_name = mb_strtoupper($value, 'UTF-8');
    		$permission->description = "Module: {$action->controller->module->id}, Controller: {$action->controller->id}, Action: " . preg_replace("/^({$cache_name}_)+/", '', $value) . "'s permission.";
    		$auth->add($permission);
    	}      		

    	//remove
    	foreach ($remove as $key => $value) {
    		$permission = $auth->getPermission($value);
    		$auth->remove($permission);
    	}  

    	if (!empty($add)) {
    		Yii::$app->cache->set($permission_name, array_keys($auth->getPermissions()), 1800);
    	}

	}

	private function cacheProcess($action) {
		$cache_name = "{$action->controller->module->id}_{$action->controller->id}";
        $_result = is_array($this->getAutoRuleActions($action)) ? $this->getAutoRuleActions($action) : [];
        $result = [];
        $rules = [];
        $cache = [];

        foreach ($_result as $value) {
        	$result[] = "{$cache_name}_{$value}";
        	$config = [
	                        'actions' => [$value],
	                        'allow' => true,
	                        'roles' => ["{$cache_name}_{$value}"],
	                    ];
        	$rules[] = Yii::createObject(array_merge($this->ruleConfig, $config));
				                    
        }

        $rules = array_merge($rules, $this->rules);

        $cache['result'] = $result;
        $cache['rules'] = $rules;

        return $cache;
	}    	

	private function getAutoRuleActions($action) {
        $rulesActions = [];
        $autoRuleActions = [];
        if (in_array('*', $this->autoRules)) {
        	$tmp = preg_grep ( '/^(action)(?=[A-Z]+)/' , get_class_methods($action->controller));
       		foreach ($tmp as $value) {
				$autoRuleActions[] = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', preg_replace('/^(action)+/', '', $value)));
	        }
        }
        else {
        	$autoRuleActions = $this->autoRules;
	    } 
	        foreach ($this->rules as $rule) {
	            if (isset($rule->actions)) {
	                $rulesActions = array_merge($rulesActions, $rule->actions);
	            }
	        }
        return array_diff($autoRuleActions, $rulesActions);
	}
}
