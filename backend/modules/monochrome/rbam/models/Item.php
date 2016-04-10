<?php

namespace backend\modules\monochrome\rbam\models;

use Yii;
use backend\modules\monochrome\rbam\RBAM;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for collection "item".
 *
 * @property \MongoId|string $_id
 * @property mixed $name
 * @property mixed $type
 * @property mixed $description
 */
class Item extends \yii\mongodb\ActiveRecord
{
    public $roles = [];
    public $permissions = [];
    public $fields = [];

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'rbac_item';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'display_name',
            'tag',
            'rule_name',
            'data',
            'type',
            'description',
            'created_at',
            'updated_at',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'name'], 'unique'],
            [['name', 'type', 'display_name'], 'required'],
            ['type', 'integer' ],
            ['type', 'in', 'range' => [1,2,3]],
            ['rule_name', 'default', 'value' => null],
            [['roles', 'permissions', 'fields'], 'is_array'],
            [['name', 'type', 'description', 'rule_name'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => RBAM::t('app', 'ID'),
            'name' => RBAM::t('app', 'Name'),
            'type' => RBAM::t('app', 'Type'),
            'description' => RBAM::t('app', 'Description'),
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $array = explode(',', $this->tag);
            $this->tag = $array;
        }

        return true;
    }

    public function afterSave()
    {
        $this->addChildren();
    }

    public function afterFind()
    {
        if (!is_array($this->tag)) {
            $this->tag = [];
        }
        $this->tag = join(',', $this->tag);
    }

    public function getRoles()
    {
        $auth = Yii::$app->authManager;
        $result = [];
        foreach ($auth->getRoles() as $key => $value) {
            $result[$key] = $key;
        }

        if (!empty($this->_id)) {
            unset($result[$this->_id]);
        }
        return $result;
    }

    public function getPermissions()
    {
        $auth = Yii::$app->authManager;
        $result = [];
        foreach ($auth->getPermissions() as $key => $value) {
            $result[$key] = $key;
        }

        if (!empty($this->_id)) {
            unset($result[$this->_id]);
        }
        asort($result);
        return $result;
    }

    public function getFields()
    {
        $auth = Yii::$app->authManager;
        $result = [];
        foreach ($auth->getFields() as $key => $value) {
            $result[$key] = $key;
        }

        if (!empty($this->_id)) {
            unset($result[$this->_id]);
        }
        return $result;
    }

    public function getChildren()
    {
        if (!empty($this->_id)) {
            $auth = Yii::$app->authManager;
            return array_keys($auth->getChildren($this->_id));
        }

        return [];
    }

    private function addChildren ()
    {
        if ($this->type === 1) {
            $auth = Yii::$app->authManager;
            $array = [];
            foreach ($this->roles as $key => $value) {
               $array[] = $auth->getRole($value);
            }

            foreach ($this->permissions as $key => $value) {
               $array[] = $auth->getPermission($value);
            }

            foreach ($this->fields as $key => $value) {
               $array[] = $auth->getField($value);
            }

            $role = $auth->getRole($this->_id);
            $auth->removeChildren($role);
            if (!empty($array)) {
                foreach ($array as $authManagerRole) {
                    $auth->addChild($role, $authManagerRole);
                }
            }
        }
    }
}    
