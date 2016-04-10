<?php

namespace common\modules\monochrome\alert\components;

use Yii;
use yii\helpers\Url;
use common\modules\monochrome\alert\Alert;
use common\modules\monochrome\alert\models\BaseAlert;
use frontend\modules\project_management\monochrome\order\models\Order;
use frontend\modules\project_management\monochrome\project\models\Project;

class OrderAlertWidget extends AlertWidget
{
    private $_projectInfo = [];

    public function init()
    {
        if (in_array('order', Yii::$app->user->getVendor()->module)) {
            parent::init();
        }
    }

    protected function setAlertInfo()
    {
        $now = time();
        $orderIds = [];
        $orderAlertSettings = $this->getOrderAlertSettings(isset(Yii::$app->user->identity->pid) ? (array)Yii::$app->user->identity->pid : []);

        foreach ($this->_projectInfo as $pid => $name) {
            $deposit = $downPayment = [];

            if (isset($orderAlertSettings[$pid]['deposit_day']) && $orderAlertSettings[$pid]['deposit_day'] > 0) {
                $deposit = Yii::$app->mongodb->getCollection(BaseAlert::collectionName())->aggregate(
                    [
                        '$match' => [
                            'category' => BaseAlert::CATEGORY_ORDER_ALERT_FOR_DEPOSIT,
                            'type' => BaseAlert::TYPE_PROJECT,
                            'type_item' => $pid,
                            'date' => [
                                '$lte' => ($now - ($orderAlertSettings[$pid]['deposit_day'] * 86400 + 86400)),
                            ],
                        ]
                    ]
                );
            }

            if (isset($orderAlertSettings[$pid]['down_payment_day']) && $orderAlertSettings[$pid]['down_payment_day'] > 0) {
                $downPayment = Yii::$app->mongodb->getCollection(BaseAlert::collectionName())->aggregate(
                    [
                        '$match' => [
                            'category' => BaseAlert::CATEGORY_ORDER_ALERT_FOR_DOWN_PAYMENT,
                            'type' => BaseAlert::TYPE_PROJECT,
                            'type_item' => $pid,
                            'date' => [
                                '$lte' => ($now - ($orderAlertSettings[$pid]['down_payment_day'] * 86400 + 86400)),
                            ],
                        ]
                    ]
                );
            }

            foreach (array_merge($deposit, $downPayment) as $alert) {
                $orderIds[] = new \MongoId($alert['assign_item']);
            }
        }

        foreach (Order::find()->where(['_id' => ['$in' => $orderIds]])->orderby('current_status_date ASC, status DESC')->all() as $order) {
            //$fixDate = is_string($order->current_status_date) ? strtotime($order->current_status_date) : $order->current_status_date;
            //$_expireDay = ((time() - $fixDate) / 86400);
            
            $_expireDay = ((time() - $order->current_status_date) / 86400);
            $expireDay = $_expireDay < 1 ? 1 : floor($_expireDay);

            $this->total++;
            $this->items[] = [
                'label' => Alert::t('app', 'Alert you to trace date {order_status_date}, status {order_status}, order {order_name}', [
                    'order_status_date' => Yii::$app->formatter->asWDateNoYear($order->current_status_date),
                    'order_status' => Order::getStatusCatalog()[$order->status],
                    'order_name' => $order->getName(),
                ]).' '.'<label class="label label-danger mark-label">'.$expireDay.Yii::t('common/app', 'Day').'</label>',
                'url' => Url::toRoute(['/order/order/update', 'pid' => $order->pid, 'id' => (string)$order->_id]),
            ];
        }
    }

    private function getOrderAlertSettings($projectIds)
    {
        $now = time();
        $ids = $result = [];

        foreach ($projectIds as $pid) {
            $ids[] = new \MongoId($pid);
        }

        $projects = Project::find()->where([
            '_id' => [
                '$in' => $ids,
            ],
            '$and' => [
                ['active_date' => ['$lte' => $now]],
                ['expire_date' => ['$gte' => $now]],
            ],
            'active' => 1,
        ])->select(['name', 'settings'])->asArray()->all();
        foreach ($projects as $project) {
            $projectId = (string)$project['_id'];
            $this->_projectInfo[$projectId] = $project['name'];

            if (isset($project['settings']['alert'])) {
                $result[$projectId]['deposit_day'] = isset($project['settings']['alert']['deposit_day']) ? $project['settings']['alert']['deposit_day'] : Yii::$app->getModule('alert')->getDefaultOrderAlertDay()['deposit_day'];
                $result[$projectId]['down_payment_day'] = isset($project['settings']['alert']['down_payment_day']) ? $project['settings']['alert']['down_payment_day'] : Yii::$app->getModule('alert')->getDefaultOrderAlertDay()['down_payment_day'];
            } else {
                $result[$projectId] = Yii::$app->getModule('alert')->getDefaultOrderAlertDay();
            }
        }

        return $result;
    }
}
