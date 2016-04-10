<?php

namespace common\components\mark;

use Yii;
use yii\mongodb\ActiveRecord;

abstract class PriorityModel extends ActiveRecord
{
    private $_priority;

    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            'priority',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'priority' => Yii::t('common/commission_agents', 'Priority'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['priority', 'required'],
            ['priority', 'filter', 'filter' => 'intval'],
            ['priority', 'integer', 'min' => 1, 'max' => (self::getCountNumber() + 1), 'on' => 'create'],
            ['priority', 'integer', 'min' => 1, 'max' => self::getCountNumber(), 'on' => 'update'],
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->_priority = $this->priority;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            if ($this->priority != self::getCountNumber()) {
                Yii::$app->mongodb->getCollection(static::collectionName())->update(
                    array_merge(static::basicQuery(), [
                        '_id' => ['$ne' => $this->_id],
                        'priority' => ['$gte' => $this->priority]
                    ]),
                    [
                        '$inc' => [
                            'priority' => 1
                        ]
                    ],
                    [
                        'multi' => true
                    ]
                );
            }
        } else {
            if ($this->_priority > $this->priority) {
                Yii::$app->mongodb->getCollection(static::collectionName())->update(
                    array_merge(static::basicQuery(), [
                        '_id' => ['$ne' => $this->_id],
                        '$and' => [
                            ['priority' => ['$gte' => $this->priority]],
                            ['priority' => ['$lt' => $this->_priority]]
                        ]
                    ]),
                    [
                        '$inc' => [
                            'priority' => 1
                        ]
                    ],
                    [
                        'multi' => true
                    ]
                );
            }

            if ($this->_priority < $this->priority) {
                Yii::$app->mongodb->getCollection(static::collectionName())->update(
                    array_merge(static::basicQuery(), [
                        '_id' => ['$ne' => $this->_id],
                        '$and' => [
                            ['priority' => ['$gt' => $this->_priority]],
                            ['priority' => ['$lte' => $this->priority]]
                        ]
                    ]),
                    [
                        '$inc' => [
                            'priority' => -1
                        ]
                    ],
                    [
                        'multi' => true
                    ]
                );
            }
        }
    }

    public function delete()
    {
        $_id = $this->_id;
        $priority = $this->priority;
        $countNumber = self::getCountNumber();

        if (parent::delete() > 0 && $priority < $countNumber) {
            Yii::$app->mongodb->getCollection(static::collectionName())->update(
                array_merge(static::basicQuery(), [
                    '_id' => ['$ne' => $_id],
                    'priority' => ['$gt' => $priority]
                ]),
                [
                    '$inc' => [
                        'priority' => -1
                    ]
                ],
                [
                    'multi' => true
                ]
            );
        }
    }

    public static function getPriorityOptions($forCreate = false)
    {
        $result = [];
        $priority = self::getCountNumber();

        if ($forCreate) {
            $priority += 1;
        }

        for ($index = 1 ; $index <= $priority ; $index++) {
            $result[$index] = $index;
        }

        return $result;
    }

    public static function getCountNumber()
    {
        return static::find()->where(static::basicQuery())->count();
    }

    abstract protected static function basicQuery();
}
