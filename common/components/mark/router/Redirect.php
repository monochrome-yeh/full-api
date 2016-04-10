<?php

namespace common\components\mark\router;

use Yii;
use \MongoId;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;
use yii\web\Cookie;

class Redirect extends ActionFilter
{
    private static $_checkMongoId = null;

    private $_pid;

    public function init()
    {
        $this->_pid = !empty(Yii::$app->request->get('pid')) && MongoId::isValid(Yii::$app->request->get('pid')) ? Yii::$app->request->get('pid') : null;
        parent::init();
    }

    public function exist()
    {
        return $this->_pid;
    }

    public function go($path = null)
    {
        if ($this->_pid == null) {
            Yii::$app->getResponse()->redirect(['/']);
        }
                    
        if ($path != null) {
            return [$path, 'pid' => $this->_pid];
        }
    }

    public function isOverview($pid, $withQuery = false)
    {
        $url = ['/project/project/overview', 'pid' => $pid];

        if (Yii::$app->user->isSales()) {
             $url = ['/log-system/sales-log-book/index', 'pid' => $pid];
        }
        if (Yii::$app->user->isAccountant()) {
             $url = ['/sale-bonus/bonus-order/overview', 'pid' => $pid];
        }
        if (Yii::$app->user->isOwner()) {
             $url = ['/analytics/report/index', 'pid' => $pid];
        }

        $queryParams = Yii::$app->request->getQueryParams();
        $currentParseUrl = parse_url(Yii::$app->request->absoluteUrl);

        if (self::$_checkMongoId === null) {
            self::$_checkMongoId = 0;

            if ($queryParams != null && isset($queryParams['pid'])) {
                foreach ($queryParams as $key => $value) {
                    if (is_string($value) && MongoId::isValid($value)) {
                        self::$_checkMongoId++;
                    }
                }
            }
        }

        if (self::$_checkMongoId == 1) {
            $url = (array_key_exists('query', $currentParseUrl) && $withQuery) ?
                [str_replace($queryParams['pid'], $pid, $currentParseUrl['path']).'?'.$currentParseUrl['query']] :
                [str_replace($queryParams['pid'], $pid, $currentParseUrl['path'])];
        }

        return $url;
    }
}
