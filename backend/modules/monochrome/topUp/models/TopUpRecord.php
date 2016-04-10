<?php

namespace backend\modules\monochrome\topUp\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use backend\modules\monochrome\topUp\TopUp;
use common\modules\monochrome\members\models\Vendor;
use common\modules\monochrome\members\models\VendorUser;
use frontend\modules\project_management\monochrome\project\models\ProjectSuper;

/**
 * This is the model class for collection "top_up_record".
 *
 * @property \MongoId|string $_id
 * @property mixed $vid
 * @property mixed $creator
 * @property mixed $price
 * @property mixed $month
 * @property mixed $note
 * @property mixed $status
 */
class TopUpRecord extends ActiveRecord
{
    const STATUS_DEAL = 1;
    const STATUS_CANCEL = 0;

    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'top_up_record';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'vid',
            'creator',
            'price',
            'month',
            'note',
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
            '_id' => TopUp::t('app', 'ID'),
            'vid' => TopUp::t('app', 'Vendor'),
            'creator' => TopUp::t('app', 'Creator'),
            'price' => TopUp::t('app', 'Price'),
            'month' => TopUp::t('app', 'Month'),
            'note' => TopUp::t('app', 'Note'),
            'status' => TopUp::t('app', 'Status'),
            'created_at' => Yii::t('common/app', 'Created At'),
            'updated_at' => Yii::t('common/app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['vid', 'check_vendor'],
            ['creator', 'check_creator'],
            ['creator', 'filter', 'filter' => function($value) { return Yii::$app->user->getId(); }],
            ['status', 'filter', 'filter' => function($value) { return self::STATUS_DEAL; }, 'on' => 'create'],
            [['status', 'price', 'month'], 'filter', 'filter' => 'intval'],
            [['price', 'month'], 'integer'],
            [['price', 'month'], 'compare', 'compareValue' => 0, 'operator' => '>'],
            ['status', 'in', 'range' => array_keys(static::getStatusOptions())],
            [['vid', 'creator', 'price', 'month', 'note', 'status'], 'required'],
            ['note', 'trim'],
        ];
    }

    public function check_vendor($attribute, $params)
    {

    }

    public function check_creator($attribute, $params)
    {

    }

    public function cancel()
    {
        if ($this->status) {
            $this->status = self::STATUS_CANCEL;
            if ($this->save(true, ['status', 'updated_at'])) {
                $useDays = ProjectSuper::getUseDays($this->vid);
                $monthQuota = static::getVendorMonthQuota($this->vid);

                $mail = [];
                $overDays = 0;
                $realUseDays = 0;
                $currentTime = time();
                $todayUnixTime = strtotime(Yii::$app->formatter->asDate($currentTime));
                $projectSupers = ProjectSuper::find()->where(['vid' => $this->vid, 'expire_date' => ['$gt' => $currentTime]])->orderby('created_at ASC')->all();
                $beforeSetProjects = [];
                $template;

                foreach ($projectSupers as $project) {
                    $overDays += (($project->expire_date - ($todayUnixTime + 86400)) / 86400);

                    $id = (string)$project->_id;
                    $beforeSetProjects[$id]['name'] = $project->name;
                    $beforeSetProjects[$id]['record'] = $project->record;
                }

                if ($useDays > ($daysQuota = $monthQuota * 30)) {
                    $realUseDays = $useDays - $overDays;
                    $afterSetProjects = [];

                    if ($realUseDays > $daysQuota) {
                        foreach ($projectSupers as $project) {
                            $realUseDays--;

                            $project->expire_date = $todayUnixTime;
                            $project->save(false, ['expire_date', 'record']);

                            $id = (string)$project->_id;
                            $afterSetProjects[$id]['name'] = $project->name;
                            $afterSetProjects[$id]['record'] = $project->record;
                        }
                    } else {
                        foreach ($projectSupers as $project) {
                            $project->expire_date = $todayUnixTime + 86400;
                            $project->save(false, ['expire_date', 'record']);

                            $id = (string)$project->_id;
                            $afterSetProjects[$id]['name'] = $project->name;
                            $afterSetProjects[$id]['record'] = $project->record;
                        }
                    }

                    $template = 'cancel';
                    $mail = [
                        'price' => $this->price,
                        'month' => $this->month,
                        'realUseDays' => $realUseDays,
                        'monthQuota' => $monthQuota,
                        'theRestOfDays' => $daysQuota - $realUseDays,
                        'beforeSetProjects' => $beforeSetProjects,
                        'afterSetProjects' => $afterSetProjects,
                    ];
                } else {
                    $template = 'just_cancel';
                    $mail = [
                        'price' => $this->price,
                        'month' => $this->month,
                        'useDays' => $useDays,
                        'monthQuota' => $monthQuota,
                        'theRestOfDays' => $daysQuota - $useDays,
                        'beforeSetProjects' => $beforeSetProjects,
                    ];
                }

                $vendor = Vendor::find()->where(['_id' => $this->vid])->one();
                if ($vendor != null) {
                    $vendorAdmin = VendorUser::find()->where(['_id' => $vendor->admin])->one();
                    if ($vendorAdmin != null) {
                        $mail['vendor_name'] = $vendor->name;

                        Yii::$app->mailer->compose('@backend/modules/monochrome/topUp/templates/'.$template, $mail)
                            ->setSubject(TopUp::t('app', 'Top Up Record Cancel Notification'))
                            ->setFrom([Yii::$app->params['adminEmail'] => '樂誌科技'])
                            ->setTo($vendorAdmin->email)
                            ->send();
                    }
                }
            }
        }
    }

    public static function getVendorMonthQuota($vid)
    {
        $result = Yii::$app->mongodb->getCollection(static::collectionName())->aggregate(
            [
                '$match' => [
                    'vid' => $vid,
                    'status' => self::STATUS_DEAL,
                ],
            ],
            [
                '$group' => [
                    '_id' => null,
                    'total' => ['$sum' => '$month'],
                ],
            ]
        );

        return isset($result[0]['total']) ? $result[0]['total'] : 0;
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_CANCEL => TopUp::t('app', 'Cancel'),
            self::STATUS_DEAL => TopUp::t('app', 'Deal'),
        ];
    }
}
