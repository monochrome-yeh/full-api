<?php

namespace common\components\monochrome\rbac; 
 
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use yii\caching\Cache;
use yii\di\Instance;
use yii\mongodb\Connection;
use yii\mongodb\Query;
use yii\rbac\Assignment;
use common\components\monochrome\rbac\BaseManager;
use common\components\monochrome\rbac\Item;
use common\components\monochrome\rbac\Field;
use common\components\monochrome\rbac\Permission;
use common\components\monochrome\rbac\Role;
use yii\rbac\Rule;
use Yii;
 
class MongoDbManager extends BaseManager
{
    /**
     * @var string|Connection
     */
    public $mongodb='mongodb';
 
    /**
     * @var string $itemChildCollection
     */
    public $itemChildCollection = 'rbac_item_child';
 
    /**
     * @var string $itemCollection
     */
    public $itemCollection = 'rbac_item';
 
    /**
     * @var string $itemCollection
     */
    public $ruleCollection = 'rbac_rule';
 
    /**
     * @var string $assignmentCollection
     */
    public $assignmentCollection = 'rbac_assignment';
    /**
     * @var Cache|array|string the cache used to improve RBAC performance. This can be one of the followings:
     *
     * - an application component ID (e.g. `cache`)
     * - a configuration array
     * - a [[yii\caching\Cache]] object
     *
     * When this is not set, it means caching is not enabled.
     *
     * Note that by enabling RBAC cache, all auth items, rules and auth item parent-child relationships will
     * be cached and loaded into memory. This will improve the performance of RBAC permission check. However,
     * it does require extra memory and as a result may not be appropriate if your RBAC system contains too many
     * auth items. You should seek other RBAC implementations (e.g. RBAC based on Redis storage) in this case.
     *
     * Also note that if you modify RBAC items, rules or parent-child relationships from outside of this component,
     * you have to manually call [[invalidateCache()]] to ensure data consistency.
     *
     * @since 2.0.3
     */
    public $cache;
    /**
     * @var string the key used to store RBAC data in cache
     * @see cache
     * @since 2.0.3
     */
    public $cacheKey = 'rbac'; 

    /**
     * @var Item[] all auth items (name => Item)
     */
    protected $items;
    /**
     * @var Rule[] all auth rules (name => Rule)
     */
    protected $rules;
    /**
     * @var array auth item parent-child relationships (childName => list of parents)
     */
    protected $parents;

    /**
     * Initializes the application component.
     * This method overrides the parent implementation by establishing the database connection.
     */
    public function init()
    {
        parent::init();
        $this->mongodb = Instance::ensure($this->mongodb, Connection::className());
    }

    /**
     * Returns the items of the specified type.
     *
     * @param integer $type the auth item type (either [[Item::TYPE_ROLE]] or [[Item::TYPE_PERMISSION]]
     * @return Item[] the auth items of the specified type.
     */
    protected function getItems($type)
    {
        $query = (new Query)
            ->from($this->itemCollection)
            ->where(['type' => $type]);
 
        $items = [];
 
        foreach ($query->all($this->mongodb) as $row) {
            $items[$row['name']] = $this->populateItem($row);
        }
 
        return $items;
    }
 
    /**
     * Adds an auth item to the RBAC system.
     *
     * @param Item $item can be either Item or Rule
     * @return boolean whether the auth item is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    protected function addItem($item)
    {
        $time = time();
        if ($item->createdAt === null) {
            $item->createdAt = $time;
        }
        if ($item->updatedAt === null) {
            $item->updatedAt = $time;
        }
        if ($item->display_name === null) {
            $item->display_name = $item->name;
        }

        $this->mongodb->getCollection($this->itemCollection)->update(['_id'=>$item->name],[
            '$setOnInsert'=>[
                'name'=>$item->name,
                'display_name' => $item->display_name,
                'tag' => $item->tag,
                'type' => $item->type,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'created_at' => $item->createdAt,
                'updated_at' => $item->updatedAt,
            ]
        ],['upsert'=>true]);
 
        return true;
    }
 
    /**
     * Returns the named auth item.
     *
     * @param string $name the auth item name.
     * @return Item the auth item corresponding to the specified name. Null is returned if no such item.
     */
    protected function getItem($name)
    {
        $row = (new Query())->from($this->itemCollection)
                            ->where(['name' => $name])
                            ->one($this->mongodb);
 
        if ($row === false) {
            return null;
        }
 
        if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
            $row['data'] = null;
        }
 
        return $this->populateItem($row);
 
    } 

    /**
     * Updates an auth item in the RBAC system.
     *
     * @param string $name the old name of the auth item
     * @param Item $item
     * @return boolean whether the auth item is successfully updated
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    protected function updateItem($name, $item)
    {
        $this->mongodb->getCollection($this->itemCollection)->update(['_id'=>$item->name],[
            '$setOnInsert'=>[
                'name'=>$item->name,
                'display_name' => $item->display_name,
                'tag' => $item->tag,                
                'type' => $item->type,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'created_at' => $item->createdAt,
                'updated_at' => $item->updatedAt,
            ]
        ],['upsert'=>true]);
 
        return true;
    }
 
    /**
     * Removes an auth item from the RBAC system.
     *
     * @param Item $item
     * @return boolean whether the role or permission is successfully removed
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    protected function removeItem($item)
    {
        $this->mongodb->getCollection($this->itemChildCollection)->remove([
            'name'=>$item->name
        ],['justOne'=>false]);
 
        $this->mongodb->getCollection($this->itemChildCollection)->update([
            'children'=>[
                '$in'=>[$item->name]
            ]
        ],['$pull'=>[
            'children'=>$item->name
        ]],['upsert'=>false, 'multi'=>true]);
 
        $this->mongodb->getCollection($this->assignmentCollection)->remove(['item_name'=>$item->name],['justOne'=>false]);
 
        $this->mongodb->getCollection($this->itemCollection)->remove(['_id'=>$item->name],['justOne'=>false]);

        $this->invalidateCache();
 
        return true;
    }
 
    /**
     * @inheritdoc
     */
    public function createPermission($name)
    {
        $permission = new Permission();
        $permission->name = $name;
        return $permission;
    }


    /**
     * @inheritdoc
     */
    public function createField($name)
    {
        $field = new Field();
        $field->name = $name;
        return $field;
    }
    /**
     * @inheritdoc
     */
    public function getField($name)
    {
        $item = $this->getItem($name);
        return $item instanceof Item && $item->type == Item::TYPE_FIELD ? $item : null;
    }

    /**
     * @inheritdoc
     */
    public function getFields()
    {
        return $this->getItems(Item::TYPE_FIELD);
    }
    /**
     * Adds a rule to the RBAC system.
     *
     * @param Rule $rule
     * @return boolean whether the rule is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     */
    protected function addRule($rule)
    {
        $time = time();
        if ($rule->createdAt === null) {
            $rule->createdAt = $time;
        }
        if ($rule->updatedAt === null) {
            $rule->updatedAt = $time;
        }
 
        $this->mongodb->getCollection($this->ruleCollection)->update(['_id'=>$rule->name],[
            '$setOnInsert'=>[
                'name'=>$rule->name,
                'data' => serialize($rule),
                'created_at' => $rule->createdAt,
                'updated_at' => $rule->updatedAt,
            ]
        ],['upsert'=>true]);
 
        return true;
    }
 
    /**
     * Removes a rule from the RBAC system.
     *
     * @param Rule $rule
     * @return boolean whether the rule is successfully removed
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     */
    protected function removeRule($rule)
    {
        $this->mongodb->getCollection($this->itemCollection)->update(['rule_name'=>$rule->name],['rule_name'=>null],['multi'=>true]);
        $this->mongodb->getCollection($this->ruleCollection)->remove(['name'=>$rule->name],['justOne'=>false]);
        $this->invalidateCache();

        return true;
    }
 
    /**
     * Updates a rule to the RBAC system.
     *
     * @param string $name the old name of the rule
     * @param Rule $rule
     * @return boolean whether the rule is successfully updated
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     */
    protected function updateRule($name, $rule)
    {
        $this->mongodb->getCollection($this->itemCollection)->update([
            'rule_name'=>$name
        ],[
            'rule_name'=>$rule->name
        ]);
 
        $rule->updatedAt = time();
 
        $this->mongodb->getCollection($this->ruleCollection)->update([
            'name'=>$name
        ],[
            'name' => $rule->name,
            'data' => serialize($rule),
            'updated_at' => $rule->updatedAt,
        ]);
 
        return true;
    }
 
    /**
     * Returns the rule of the specified name.
     *
     * @param string $name the rule name
     * @return Rule the rule object, or null if the specified name does not correspond to a rule.
     */
    public function getRule($name)
    {
        $row = (new Query)->select(['data'])
                          ->from($this->ruleCollection)
                          ->where(['name' => $name])
                          ->one($this->mongodb);
        return $row === false ? null : unserialize($row['data']);
    }
 
    /**
     * Returns all rules available in the system.
     *
     * @return Rule[] the rules indexed by the rule names
     */
    public function getRules()
    {
        $query = (new Query)->from($this->ruleCollection);
 
        $rules = [];
        foreach ($query->all($this->mongodb) as $row) {
            $rules[$row['name']] = unserialize($row['data']);
        }
 
        return $rules;
    }
 
    /**
     * Checks if the user has the specified permission.
     *
     * @param string|integer $userId the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param string $permissionName the name of the permission to be checked against
     * @param array $params name-value pairs that will be passed to the rules associated
     * with the roles and permissions assigned to the user.
     * @return boolean whether the user has the specified permission.
     * @throws \yii\base\InvalidParamException if $permissionName does not refer to an existing permission
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        $assignments = $this->getAssignments($userId);
        $this->loadFromCache();
        if ($this->items !== null) {
            return $this->checkAccessFromCache($userId, $permissionName, $params, $assignments);
        } else {
            return $this->checkAccessRecursive($userId, $permissionName, $params, $assignments);
        }
    }
 
    /**
     * Performs access check for the specified user based on the data loaded from cache.
     * This method is internally called by [[checkAccess()]] when [[cache]] is enabled.
     * @param string|integer $user the user ID. This should can be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param string $itemName the name of the operation that need access check
     * @param array $params name-value pairs that would be passed to rules associated
     * with the tasks and roles assigned to the user. A param with name 'user' is added to this array,
     * which holds the value of `$userId`.
     * @param Assignment[] $assignments the assignments to the specified user
     * @return boolean whether the operations can be performed by the user.
     * @since 2.0.3
     */
    protected function checkAccessFromCache($user, $itemName, $params, $assignments)
    {
        if (!isset($this->items[$itemName])) {
            return false;
        }

        $item = $this->items[$itemName];

        Yii::trace($item instanceof Role ? "Checking role: $itemName" : "Checking permission: $itemName", __METHOD__);

        if (!$this->executeRule($user, $item, $params)) {
            return false;
        }

        if (isset($assignments[$itemName]) || in_array($itemName, $this->defaultRoles)) {
            return true;
        }

        if (!empty($this->parents[$itemName])) {
            foreach ($this->parents[$itemName] as $parent) {
                if ($this->checkAccessRecursive($user, $parent, $params, $assignments)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Performs access check for the specified user.
     * This method is internally called by [[checkAccess()]].
     * @param string|integer $user the user ID. This should can be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param string $itemName the name of the operation that need access check
     * @param array $params name-value pairs that would be passed to rules associated
     * with the tasks and roles assigned to the user. A param with name 'user' is added to this array,
     * which holds the value of `$userId`.
     * @param Assignment[] $assignments the assignments to the specified user
     * @return boolean whether the operations can be performed by the user.
     */
    protected function checkAccessRecursive($user, $itemName, $params, $assignments)
    {
        if (($item = $this->getItem($itemName)) === null) {
            return false;
        }
 
        Yii::trace($item instanceof Role ? "Checking role: $itemName" : "Checking permission: $itemName", __METHOD__);
 
        if (!$this->executeRule($user, $item, $params)) {
            return false;
        }
 
        if (isset($assignments[$itemName]) || in_array($itemName, $this->defaultRoles)) {
            return true;
        }
 
        $query = new Query;
        $parents = $query->select(['name'])
                     ->from($this->itemChildCollection)
                     ->where([
                        'children' => [
                            '$in'=>[$itemName]
                        ]
                    ])->all($this->mongodb);
 
        foreach ($parents as $parent) {
            if ($this->checkAccessRecursive($user, $parent['name'], $params, $assignments)) {
                return true;
            }
        }
 
        return false;
    }
 
    /**
     * Returns the roles that are assigned to the user via [[assign()]].
     * Note that child roles that are not assigned directly to the user will not be returned.
     *
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @return Role[] all roles directly or indirectly assigned to the user. The array is indexed by the role names.
     */
    public function getRolesByUser($userId)
    {
        if($userId instanceof \MongoId || $userId instanceof \MongoDB\BSON\ObjectID){
            $userId = (string)$userId;
        }
 
        $assignments = (new Query())
            ->from($this->assignmentCollection)
            ->where(['user_id'=>$userId])
            ->select(['item_name'])
            ->all($this->mongodb);
 
        $roles = [];
        foreach ($assignments as $row) {
            $roles[$row['item_name']] = $this->getItem($row['item_name']);
        }
        return $roles;
    }
 
    /**
     * Returns all permissions that the specified role represents.
     *
     * @param string $roleName the role name
     * @return Permission[] all permissions that the role represents. The array is indexed by the permission names.
     */
    public function getPermissionsByRole($roleName)
    {
        $childrenList = $this->getChildrenList();
        $result = [];
        $this->getChildrenRecursive($roleName, $childrenList, $result);
        if (empty($result)) {
            return [];
        }
        $query = (new Query)->from($this->itemCollection)->where([
            'type' => Item::TYPE_PERMISSION,
            'name' => array_keys($result),
        ]);
        $permissions = [];
        foreach ($query->all($this->mongodb) as $row) {
            $permissions[$row['name']] = $this->populateItem($row);
        }
        return $permissions;
    }
 
    /**
     * Returns all permissions that the user has.
     *
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @return Permission[] all permissions that the user has. The array is indexed by the permission names.
     */
    public function getPermissionsByUser($userId)
    {
        if($userId instanceof \MongoId || $userId instanceof \MongoDB\BSON\ObjectID){
            $userId = (string)$userId;
        }
 
        $permissions = (new Query)
            ->select(['item_name'])
            ->from($this->assignmentCollection)
            ->where(['user_id' => (string)$userId])
            ->all($this->mongodb);
 
        $childrenList = $this->getChildrenList();
        $result = [];
 
        foreach ($permissions as $permission) {
            $this->getChildrenRecursive($permission['item_name'], $childrenList, $result);
        }
 
        if (empty($result)) {
            return [];
        }
 
        $query = (new Query)->from($this->itemCollection)->where([
            'type' => Item::TYPE_PERMISSION,
            '_id' => array_keys($result),
        ]);
        $permissions = [];
        foreach ($query->all($this->mongodb) as $row) {
            $permissions[$row['name']] = $this->populateItem($row);
        }
        return $permissions;
    }
 
    /**
     * Returns the children for every parent.
     * @return array the children list. Each array key is a parent item name,
     * and the corresponding array value is a list of child item names.
     */
    protected function getChildrenList()
    {
        $query = (new Query)->from($this->itemChildCollection);
        $parents = [];
 
        foreach ($query->all($this->mongodb) as $row) {
            $parents[$row['name']] = $row['children'];
        }
 
        return $parents;
    }
 
    /**
     * Recursively finds all children and grand children of the specified item.
     * @param string $name the name of the item whose children are to be looked for.
     * @param array $childrenList the child list built via [[getChildrenList()]]
     * @param array $result the children and grand children (in array keys)
     */
    protected function getChildrenRecursive($name, $childrenList, &$result)
    {
        if (isset($childrenList[$name])) {
            foreach ($childrenList[$name] as $child) {
                $result[$child] = true;
                $this->getChildrenRecursive($child, $childrenList, $result);
            }
        }
    }
 
 
 
    /**
     * Adds an item as a child of another item.
     * @url http://docs.mongodb.org/manual/tutorial/model-tree-structures-with-child-references/
     *
     * @param Item $parent
     * @param Item $child
     * @throws \yii\base\InvalidParamException
     * @throws \yii\base\InvalidCallException
     * @return bool
     */
    public function addChild($parent, $child)
    {
        if ($parent->name === $child->name) {
            throw new InvalidParamException("Cannot add '{$parent->name}' as a child of itself.");
        }
 
        if ($parent instanceof Permission && $child instanceof Role) {
            throw new InvalidParamException("Cannot add a role as a child of a permission.");
        }
 
        if ($this->detectLoop($parent, $child)) {
            throw new InvalidCallException("Cannot add '{$child->name}' as a child of '{$parent->name}'. A loop has been detected.");
        }
 
        $this->mongodb->getCollection($this->itemChildCollection)->update(['_id'=>$parent->name],[
            'name'=>$parent->name,
        ],['upsert'=>true]);
 
        $this->mongodb->getCollection($this->itemChildCollection)->update(['_id'=>$parent->name],[
            '$addToSet'=>[
                'children'=>$child->name
            ]
        ],['upsert'=>false]);
 
        return true;
    }
 
    /**
     * Removes a child from its parent.
     * Note, the child item is not deleted. Only the parent-child relationship is removed.
     *
     * @param Item $parent
     * @param Item $child
     * @return boolean whether the removal is successful
     */
    public function removeChild($parent, $child)
    {
        $this->mongodb->getCollection($this->itemChildCollection)->update(['_id'=>$parent->name],[
            '$pull'=>[
                'children'=>$child->name
            ]
        ]);
    
        $this->invalidateCache();

        return true;
    }
 
    /**
     * Removes children from its parent.
     * Note, the children items are not deleted. Only the parent-child relationships are removed.
     *
     * @param Item $parent
     * @return boolean whether the removal is successful
     */
    public function removeChildren($parent)
    {
        $this->mongodb->getCollection($this->itemChildCollection)->update(['_id'=>$parent->name],['$set' => ['children' => []]]);
 
        $this->invalidateCache();

        return true;
    }
 
    /**
     * Returns a value indicating whether the child already exists for the parent.
     *
     * @param Item $parent
     * @param Item $child
     * @return boolean whether `$child` is already a child of `$parent`
     */
    public function hasChild($parent, $child)
    {
        return (new Query)
            ->from($this->itemChildCollection)
            ->where([
                '_id' => $parent->name,
                'children' => ['$in'=>[$child->name]]
            ])
            ->one($this->mongodb) !== false;
 
    }
 
    /**
     * Returns the child permissions and/or roles.
     *
     * @param string $name the parent name
     * @return Item[] the child permissions and/or roles
     */
    public function getChildren($name)
    {
        $query = (new Query)
            ->from($this->itemChildCollection)
            ->where([
                '_id' => $name,
            ]);
 
        $result = $query->one();
 
        $children = [];
 
        if($result !== false && !empty($result)){
            foreach ($result['children'] as $child) {
                $children[$child] = $this->getItem($child);
            }
        }
 
        return $children;
    }
 
    /**
     * Assigns a role to a user.
     *
     * @param Role $role
     * @param string $userId the user ID (see [[\yii\web\User::id]])
     * @return Assignment the role assignment information.
     * @throws \Exception if the role has already been assigned to the user
     */
    public function assign($role, $userId)
    {
        if($userId instanceof \MongoId || $userId instanceof \MongoDB\BSON\ObjectID){
            $userId = (string)$userId;
        }
 
        $assignment = new Assignment([
            'userId' => $userId,
            'roleName' => $role->name,
            'createdAt' => time(),
        ]);
 
        $this->mongodb->getCollection($this->assignmentCollection)->update([
            '_id'=>"{$assignment->userId}"
        ],[
            '$setOnInsert'=>[
                'aid'=>"{$assignment->userId}-$assignment->roleName",
                'user_id' => $assignment->userId,
                'item_name' => $assignment->roleName,
                'created_at' => $assignment->createdAt,
            ]
        ],['upsert'=>true]);
 
        return $assignment;
    }
 
    /**
     * Revokes a role from a user.
     *
     * @param Role $role
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @return boolean whether the revoking is successful
     */
    public function revoke($role, $userId)
    {
        if($userId instanceof \MongoId || $userId instanceof \MongoDB\BSON\ObjectID){
            $userId = (string)$userId;
        }
 
        return $this->mongodb->getCollection($this->assignmentCollection)->remove([
            'aid'=>"{$userId}-{$role->name}"
        ])!==false;
    }
 
    /**
     * Revokes all roles from a user.
     *
     * @param mixed $userId the user ID (see [[\yii\web\User::id]])
     * @return boolean whether the revoking is successful
     */
    public function revokeAll($userId)
    {
        if($userId instanceof \MongoId || $userId instanceof \MongoDB\BSON\ObjectID){
            $userId = (string)$userId;
        }
 
        return $this->mongodb->getCollection($this->assignmentCollection)->remove([
            'user_id'=>$userId
        ],['justOne'=>false])!==false;
    }
 
    /**
     * Returns the assignment information regarding a role and a user.
     *
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @param string $roleName the role name
     * @return Assignment the assignment information. Null is returned if
     * the role is not assigned to the user.
     */
    public function getAssignment($roleName, $userId)
    {
        $row = (new Query)->from($this->assignmentCollection)
                          ->where(['aid'=>"{$userId}-$roleName"])
                          ->one($this->mongodb);
 
        if ($row === false) {
            return null;
        }
 
        return new Assignment([
            'userId' => $row['user_id'],
            'roleName' => $row['item_name'],
            'createdAt' => $row['created_at'],
        ]);
    }
 
    /**
     * Returns all role assignment information for the specified user.
     *
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @return Assignment[] the assignments indexed by role names. An empty array will be
     * returned if there is no role assigned to the user.
     */
    public function getAssignments($userId)
    {
        if($userId instanceof \MongoId || $userId instanceof \MongoDB\BSON\ObjectID){
            $userId = (string)$userId;
        }
 
        $query = (new Query)
            ->from($this->assignmentCollection)
            ->where(['user_id' => (string)$userId]);
 
        $assignments = [];
        foreach ($query->all($this->mongodb) as $row) {
            $assignments[$row['item_name']] = new Assignment([
                'userId' => $row['user_id'],
                'roleName' => $row['item_name'],
                'createdAt' => $row['created_at'],
            ]);
        }
 
        return $assignments;
    }

    /**
     * Removes all authorization data, including roles, permissions, rules, and assignments.
     */
    public function removeAll()
    {
        $this->removeAllAssignments();
        $this->mongodb->getCollection($this->itemCollection)->remove([],['justOne'=>false]);
        $this->mongodb->getCollection($this->itemChildCollection)->remove([],['justOne'=>false]);
        $this->mongodb->getCollection($this->ruleCollection)->remove([],['justOne'=>false]);
        $this->invalidateCache();
    }
     /**
     * Removes all permissions.
     * All parent child relations will be adjusted accordingly.
     */
    public function removeAllFields()
    {
        $type = Item::TYPE_Field;
        $names = $this->getAllItemsName($type);
        $this->mongodb->getCollection($this->itemChildCollection)->update([
            'children'=>[
                '$in'=>$names
            ]
        ],[
            '$pullAll'=>[
                'children'=>$names
            ]
        ],['multi'=>true]);
 
        $this->removeAllItems($type,$names);
    }
    /**
     * Removes all permissions.
     * All parent child relations will be adjusted accordingly.
     */
    public function removeAllPermissions()
    {
        $type = Item::TYPE_PERMISSION;
        $names = $this->getAllItemsName($type);
        $this->mongodb->getCollection($this->itemChildCollection)->update([
            'children'=>[
                '$in'=>$names
            ]
        ],[
            '$pullAll'=>[
                'children'=>$names
            ]
        ],['multi'=>true]);
 
        $this->removeAllItems($type,$names);
    }
 
    /**
     * Removes all roles.
     * All parent child relations will be adjusted accordingly.
     */
    public function removeAllRoles()
    {
        $type = Item::TYPE_ROLE;
        $names = $this->getAllItemsName($type);
 
        $this->mongodb->getCollection($this->itemChildCollection)->update([
            'children'=>[
                '$in'=>$names
            ]
        ],[
            '$pullAll'=>[
                'children'=>$names
            ]
        ],['multi'=>true]);
 
        $this->mongodb->getCollection($this->itemChildCollection)->remove([
            'name'=>[
                '$in'=>$names
            ]
        ],['justOne'=>false]);
 
        $this->removeAllItems($type,$names);
    }
 
    protected function getAllItemsName($type)
    {
        $items = (new Query)
            ->select(['name'])
            ->from($this->itemCollection)
            ->where(['type' => $type])
            ->all($this->mongodb);
        $names = [];
        foreach($items as $item){
            $names[]=$item['name'];
        }
        return $names;
    }
 
    /**
     * Removes all auth items of the specified type.
     *
     * @param integer $type the auth item type (either Item::TYPE_PERMISSION or Item::TYPE_ROLE)
     * @param array $names
     */
    protected function removeAllItems($type, array $names)
    {
        $this->mongodb->getCollection($this->assignmentCollection)->remove([
            'item_name'=>[
                '$in'=>$names
            ]
        ],['multi'=>true]);
 
        $this->mongodb->getCollection($this->itemCollection)->remove(['type'=>$type],['multi'=>true]);

        $this->invalidateCache();
 
    }
 
    /**
     * Removes all rules.
     * All roles and permissions which have rules will be adjusted accordingly.
     */
    public function removeAllRules()
    {
        $this->mongodb->getCollection($this->itemCollection)->update([],['ruleName'=>null],['multi'=>true]);
        $this->mongodb->getCollection($this->ruleCollection)->remove([],['justOne'=>false]);

        $this->invalidateCache();
    }
 
    /**
     * Removes all role assignments.
     */
    public function removeAllAssignments()
    {
        $this->mongodb->getCollection($this->assignmentCollection)->remove([],['justOne'=>false]);
    }
 
    /**
     * Populates an auth item with the data fetched from database
     * @param array $row the data from the auth item table
     * @return Item the populated auth item instance (either Role or Permission)
     */
    protected function populateItem($row)
    {
        $class = $row['type'] == Item::TYPE_PERMISSION ? Permission::className() : Role::className();
 
        if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
            $data = null;
        }
        return new $class([
            'name' => $row['name'],
            'type' => $row['type'],
            'display_name' => $row['display_name'],
            'tag' => $row['tag'],            
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $data,
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
        ]);
    }
 
    /**
     * Checks whether there is a loop in the authorization item hierarchy.
     * @param Item $parent the parent item
     * @param Item $child the child item to be added to the hierarchy
     * @return boolean whether a loop exists
     */
    protected function detectLoop($parent, $child)
    {
        if ($child->name === $parent->name) {
            return true;
        }
 
        foreach ($this->getChildren($child->name) as $grandchild) {
            if ($this->detectLoop($parent, $grandchild)) {
                return true;
            }
        }
        return false;
    }
 
    public function invalidateCache()
    {
        if ($this->cache !== null) {
            $this->cache->delete($this->cacheKey);
            $this->items = null;
            $this->rules = null;
            $this->parents = null;
        }
    }

    public function loadFromCache()
    {
        if ($this->items !== null || !$this->cache instanceof Cache) {
            return;
        }

        $data = $this->cache->get($this->cacheKey);
        if (is_array($data) && isset($data[0], $data[1], $data[2])) {
            list ($this->items, $this->rules, $this->parents) = $data;
            return;
        }

        $query = (new Query)
            ->from($this->itemCollection)
            ->all($this->mongodb);
        foreach ($query as $row) {
            $this->items[$row['name']] = $this->populateItem($row);
        }

        $query = (new Query)
            ->from($this->ruleCollection)
            ->all($this->mongodb);

        foreach ($query as $row) {
            $this->rules[$row['name']] = unserialize($row['data']);
        }


        $query = (new Query)
            ->from($this->itemChildCollection)
            ->all($this->mongodb);
        $this->parents = [];

        foreach ($query as $row) {
            if (isset($this->items[$row['child']])) {
                $this->parents[$row['child']][] = $row['parent'];
            }
        }

        $this->cache->set($this->cacheKey, [$this->items, $this->rules, $this->parents]);
    } 
    /**
     * @inheritdoc
     */
    public function canAddChild($parent, $child)
    {
        return !$this->detectLoop($parent, $child);
    }
    /**
     * Returns all role assignment information for the specified role.
     * @param string $roleName
     * @return Assignment[] the assignments. An empty array will be
     * returned if role is not assigned to any user.
     * @since 2.0.7
     */
    public function getUserIdsByRole($roleName)
    {
        if (empty($roleName)) {
            return [];
        }

        return (new Query)->select('[[user_id]]')
            ->from($this->assignmentTable)
            ->where(['item_name' => $roleName])->column($this->db);
    }    
}
