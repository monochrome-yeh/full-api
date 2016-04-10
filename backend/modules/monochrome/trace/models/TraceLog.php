<?php

namespace backend\modules\monochrome\trace\models;

use Yii;
use yii\log\Logger;
use yii\web\Request;
use common\modules\monochrome\members\models\User;
use common\modules\monochrome\members\models\AdminUser;
use common\modules\monochrome\members\models\Vendor;

/**
 * This is the model class for collection "trace_log".
 *
 * @property \MongoId|string $_id
 * @property mixed $level
 * @property mixed $category
 * @property mixed $prefix
 * @property mixed $message
 * @property mixed $log_time
 */
class TraceLog extends \yii\mongodb\ActiveRecord
{
    public $type;
    const NORMAL_USER = 10;
    const VENDOR_USER = 20;
    const ADMIN_USER = 30;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'trace_log';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'level',
            'category',
            'prefix',
            'message',
            'log_time',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category', 'message'], 'required'],
            ['category', 'in', 'range' => array_keys(static::getCategory())],
            ['level', 'filter', 'filter' => function($value) {
                return Logger::LEVEL_INFO;
            }],
            ['prefix', 'filter', 'filter' => function($value) {
                $request = Yii::$app->getRequest();
                $ip = $request instanceof Request ? $request->getUserIP() : '-';

                $user = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
                $userID = ($user && ($identity = $user->getIdentity(false))) ? $userID = $identity->getId() : $userID = '-';;

                $session = Yii::$app->has('session', true) ? Yii::$app->get('session') : null;
                $sessionID = $session && $session->getIsActive() ? $session->getId() : '-';

                return "[$ip][$userID][$sessionID]";
            }],
            ['log_time', 'filter', 'filter' => function($value) {
                return microtime(true);
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'level' => 'Level',
            'category' => 'Category',
            'prefix' => '[ip] [userID] [sessionID]',
            'log_time' => 'Log Time',
            'message' => 'Message',
        ];
    }

    public function getCategory()
    {
        return array_combine(Yii::$app->log->targets['mongodb']->categories, Yii::$app->log->targets['mongodb']->categories);
    }

    public function getFilterInfo()
    {
        $result = ['filterCondition' => ['or'], 'filterDropDownList' => []];

        switch ($this->type) {
            case self::NORMAL_USER:
                foreach (User::find()->where(['login.normal' => ['$exists' => true]])->select(['_id', 'username'])->asArray()->all() as $member) {
                    $user_id = (string)$member['_id'];
                    $result['filterCondition'][] = ['like', 'prefix', $user_id];
                    $result['filterDropDownList'][$user_id] = $member['username'];
                }

                break;
            case self::VENDOR_USER:
                $vendors = Vendor::getVendorList();
                foreach (User::find()->where(['login.vendor' => ['$exists' => true]])->select(['_id', 'login', 'username'])->asArray()->all() as $member) {
                    $user_id = (string)$member['_id'];
                    $result['filterCondition'][] = ['like', 'prefix', $user_id];
                    $result['filterDropDownList'][$user_id] = isset($vendors[$member['login']['vendor']['vid']]) ? $vendors[$member['login']['vendor']['vid']].'『 '.$member['username'].' 』' : $member['username'];
                }

                break;
            case self::ADMIN_USER:
                foreach (AdminUser::find()->select(['_id', 'username'])->asArray()->all() as $member) {
                    $user_id = (string)$member['_id'];
                    $result['filterCondition'][] = ['like', 'prefix', $user_id];
                    $result['filterDropDownList'][$user_id] = $member['username'];
                }

                break;
            default:
                $result['filterCondition'] = [];
                break;
        }

        if (count($result['filterCondition']) == 1) {
            $result['filterCondition'] = ['_id' => 'nonexistence'];
        }

        return $result;
    }
}
