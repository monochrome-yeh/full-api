<?php

namespace common\components\mark\trace;

use Yii;
use yii\web\Controller;
use yii\base\Behavior;
use yii\base\ActionEvent;
use yii\helpers\VarDumper;

class ActionTrace extends Behavior
{
    public $categories = [];
    public $delimiters = '<br>';

    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }

    public function beforeAction($event)
    {
        if (!Yii::$app->user->isGuest) {
            $action = $event->action->id;

            if (count(array_diff(array_keys($this->categories), Yii::$app->log->targets['mongodb']->categories)) > 0) {
                throw new \Exception('Invalid Category');
            }

            foreach ($this->categories as $category => $actions) {
                if (in_array($action, $actions) || in_array('*', $actions)) {
                    Yii::info($this->getActionInfo(), $category);
                }
            }
        }

        return $event->isValid;
    }

    private function getActionInfo()
    {
        $headers = [
            '$_GET：'.json_encode(Yii::$app->request->get()),
            '$_POST：'.json_encode(Yii::$app->request->post()),
            'Router：'.Yii::$app->request->absoluteUrl,
            'Host：'.$_SERVER['HTTP_HOST'],
            'User_Agent：'.$_SERVER['HTTP_USER_AGENT'],
        ];

        if (isset($_SERVER['HTTP_REFERER'])) {
            $headers[] = 'Referer：'.$_SERVER['HTTP_REFERER'];
        }

        return implode($this->delimiters, $headers);
    }
}
