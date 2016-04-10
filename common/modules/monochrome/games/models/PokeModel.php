<?php

namespace common\modules\monochrome\games\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;

class PokeModel extends GameModel
{
    /**
   * @inheritdoc
   */
    public static function collectionName()
    {
        return 'game';
    }
    protected $_status = [
        'host' => 0,
        'invitee' => [],
        'pokes' => [
           [0,null],
           [0,null],
           [0,null],
           [0,null],
           [0,null],
           [0,null],
           [0,null],
           [0,null],
           [0,null],
        ],      
    ];
}
