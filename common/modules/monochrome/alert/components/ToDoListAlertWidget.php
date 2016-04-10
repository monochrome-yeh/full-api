<?php

namespace common\modules\monochrome\alert\components;

use Yii;
use yii\helpers\Url;
use common\modules\monochrome\alert\Alert;
use common\modules\monochrome\alert\models\BaseAlert;
use common\modules\monochrome\request\models\ToDoList;

class ToDoListAlertWidget extends AlertWidget
{
    public function init()
    {
    	if (in_array('request', Yii::$app->user->getVendor()->module)) {
    		parent::init();
    	}
    }

	protected function setAlertInfo()
	{
		$ids = [];
		foreach (BaseAlert::find()->where(['category' => BaseAlert::CATEGORY_TO_DO_LIST_ALERT, 'type' => BaseAlert::TYPE_USER, 'type_item' => Yii::$app->user->getId()])->select(['assign_item'])->asArray()->all() as $alert) {
			$ids[] = new \MongoId($alert['assign_item']);
		}

		if ($ids != null) {
			$assigners = ToDoList::getAssigners();
	        $toDoList = Yii::$app->mongodb->getCollection(ToDoList::collectionName())->aggregate(
	            [
		            '$match' => [
		            	'_id' => ['$in' => $ids]
	            	]
	            ],
	            [
		            '$group' => [
		                '_id' => '$assigner',
		                'count' => ['$sum' => 1]
		            ]
	            ]
	        );

	        foreach ($toDoList as $info) {
        		$this->total++;
        		$this->items[] = [
        			'label' => Alert::t('app', '{assigner} assign {total} items to you.', [
        				'assigner' => isset($assigners[$info['_id']]) ? $assigners[$info['_id']] : '',
        				'total' => $info['count'],
        			]),
        			'url' => Url::toRoute(['/request/to-do-list/index', 'ToDoListSearch[assigner]' => $info['_id'], 'ToDoListSearch[userStatus]' => ToDoList::STATUS_NOT_YET]),
        		];
	        }
		}
	}
}
