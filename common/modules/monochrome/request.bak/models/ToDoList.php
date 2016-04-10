<?php

namespace common\modules\monochrome\request\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\components\mark\TimeStandard;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\VendorUser;
use common\modules\monochrome\alert\components\ToDoListAlertBehavior;
use common\modules\monochrome\request\Request;

/**
 * This is the model class for collection "to_do_list".
 *
 * @property \MongoId|string $_id
 * @property mixed $deadline
 * @property mixed $assigner
 * @property mixed $assign_users
 * @property mixed $done_users
 * @property mixed $content
 * @property mixed $status
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class ToDoList extends ActiveRecord
{
    const STATUS_NOT_YET = 0;
    const STATUS_DONE = 1;

    private static $assigners;

    public $pids;
    public $roles;
    public $userStatus;
    public $correctPids;

    public $progress;

    public function init()
    {
        if ($this->isNewRecord) {
            $this->correctPids = $this->pids = isset(Yii::$app->user->identity->pid) ? Yii::$app->user->identity->pid : [];
            $this->roles = ['manager', 'sales'];
        }
    }

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'to_do_list';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ToDoListAlertBehavior::className(),
            TimestampBehavior::className(),
            // [
            //     'class' => TimeStandard::className(),
            //     'attributes' => [
            //         ActiveRecord::EVENT_BEFORE_INSERT => ['time' => 'deadline'],
            //         ActiveRecord::EVENT_BEFORE_UPDATE => ['time' => 'deadline'],
            //     ],
            // ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            // 'deadline',
            'assigner',
            'assign_users',
            'done_users',
            'content',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Request::t('app', 'ID'),
            'pids' => Request::t('app', 'Project List'),
            'roles' => Request::t('app', 'Role List'),
            // 'deadline' => Request::t('app', 'Deadline'),
            'assigner' => Request::t('app', 'Assigner'),
            'assign_users' => Request::t('app', 'Assign Users'),
            'done_users' => Request::t('app', 'Done Users'),
            'content' => Request::t('app', 'Content'),
            'status' => Request::t('app', 'Status'),
            'userStatus' => Request::t('app', 'User Status'),
            'progress' => Request::t('app', 'Progress'),
            'created_at' => Request::t('app', 'Created At'),
            'updated_at' => Yii::t('common/app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // ['deadline', 'date', 'format' => 'yyyy-MM-dd', 'message' => Yii::t('common/app', 'Date Format Error, Must Like 1990-01-01')],
            ['assigner', 'filter', 'filter' => function($value) { return Yii::$app->user->getId(); }, 'on' => 'create'],
            ['done_users', 'filter', 'filter' => function($value) { return []; }, 'on' => 'create'],
            ['status', 'filter', 'filter' => function($value) { return self::STATUS_NOT_YET; }, 'on' => 'create'],
            ['status', 'in', 'range' => array_keys($this->getStatusOptions())],
            ['pids', 'check_project', 'on' => 'create'],
            ['roles', 'check_role', 'on' => 'create'],
            [['pids', 'roles'], 'required', 'on' => 'create'],
            ['content', 'trim'],
            [['assigner', 'content', 'status'], 'required'],
            // [['deadline', 'assigner', 'content', 'status'], 'required'],
        ];
    }

    public function check_project($attribute, $params)
    {
        if (count(array_diff($this->$attribute, $this->correctPids)) > 0) {
            $this->addError($attribute, Request::t('app', 'Wrong Project'));
        }
    }

    public function check_role($attribute, $params)
    {
        if (count(array_diff($this->$attribute, array_keys($this->getRolesOptions()))) > 0) {
            $this->addError($attribute, Request::t('app', 'Wrong Role'));
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $result = [];

                foreach (VendorUser::find()->where([
                    'login.vendor.vid' => isset(Yii::$app->user->identity->vid) ? Yii::$app->user->identity->vid : '',
                    'status' => VendorUser::STATUS_ACTIVE,
                    'role' => ['$in' => $this->roles],
                    'pid' => ['$in' => $this->pids]
                ])->select(['_id'])->asArray()->all() as $assignUser) {
                    $result[] = (string)$assignUser['_id'];
                }

                $this->assign_users = $result;
            }

            return true;
        } else {
            return false;
        }
    }

    public function getUserStatus()
    {
        $id = Yii::$app->user->getId();
        if (in_array($id, $this->assign_users) && in_array($id, $this->done_users)) {
            return self::STATUS_DONE;
        }

        return self::STATUS_NOT_YET;
    }

    public function done()
    {
        $result = false;

        if (in_array(($id = Yii::$app->user->getId()), $this->assign_users)) {
            if (array_search($id, $this->done_users) === false) {
                $doneUsers = $this->done_users;
                $doneUsers[] = $id;
                $this->done_users = $doneUsers;
            }

            if (count(array_diff($this->assign_users, $this->done_users)) == 0) {
                $this->status = self::STATUS_DONE;
            }

            if ($this->save(true, ['done_users', 'status'])) {
                $result = true;
            }
        }

        return $result;
    }

    public static function getRolesOptions()
    {
        return [
            'manager' => Members::t('app', 'Manager'),
            'sales' => Members::t('app', 'Sales'),
        ];
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_NOT_YET => Request::t('app', 'Not Yet'),
            self::STATUS_DONE => Request::t('app', 'Done'),
        ];
    }

    public static function getAssigners()
    {
        if (self::$assigners == null) {
            $result = $myAssigners = [];

            foreach (static::find()->where(['assign_users' => Yii::$app->user->getId()])->select(['assigner'])->asArray()->all() as $value) {
                $myAssigners[] = new \MongoId($value['assigner']);
            }

            foreach (VendorUser::find()->where(['_id' => ['$in' => $myAssigners]])->select(['username'])->asArray()->all() as $value) {
                $result[(string)$value['_id']] = $value['username'];
            }

            self::$assigners = $result;
        }

        return self::$assigners;
    }

    public static function getTotalAssignToDoList($id = null)
    {
        $id = $id === null ? Yii::$app->user->getId() : (string)$id;

        return static::find()->where(['assign_users' => $id])->count();
    }

    public static function getTotalDoneToDoList($id = null)
    {
        $id = $id === null ? Yii::$app->user->getId() : (string)$id;

        return static::find()->where(['assign_users' => $id, 'done_users' => $id])->count();
    }
}
