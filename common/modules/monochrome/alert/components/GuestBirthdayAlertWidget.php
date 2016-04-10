<?php

namespace common\modules\monochrome\alert\components;

use Yii;
use yii\helpers\Url;
use common\modules\monochrome\alert\Alert;
use common\modules\monochrome\alert\models\BaseAlert;
use frontend\modules\project_management\monochrome\project\models\Project;
use frontend\modules\project_management\mark\guest\models\Guest;

class GuestBirthdayAlertWidget extends AlertWidget
{
	private $_projectInfo;

    public function init()
    {
    	if (in_array('guest', Yii::$app->user->getVendor()->module)) {
    		parent::init();
    	}
    }

    protected function setAlertInfo()
    {
		$leap = false;
		$time = time();
		$year = (int)Yii::$app->formatter->asDate($time, 'php:Y');
        if (($year % 400 == 0) || (($year % 4 == 0) && ($year % 100 != 0))) {
        	$leap = true;
        }

        $result = $guestIds = [];
        $myGuests = Guest::getMyGuestHasBirthday();
        $guestBirthdayAlertSettings = $this->getGuestBirthdayAlertSettings(array_keys($myGuests));

        if ($guestBirthdayAlertSettings != null && ($max = max($guestBirthdayAlertSettings)) > 0) {
        	$result = array_fill_keys($this->getAlertRange($max, $leap, $time), []);

	        foreach ($this->_projectInfo as $pid => $name) {
	        	if (isset($guestBirthdayAlertSettings[$pid]) && $guestBirthdayAlertSettings[$pid] > 0) {
	        		$match = [
	        			'assign_item' => ['$in' => $myGuests[$pid]],
	        			'category' => BaseAlert::CATEGORY_GUEST_BIRTHDAY_ALERT,
	        			'type' => BaseAlert::TYPE_PROJECT,
	        			'type_item' => $pid,
	        		];
		        	$alertRange = $this->getAlertRange($guestBirthdayAlertSettings[$pid], $leap, $time);

		        	if ($alertRange != null) {
			        	$alertMin = min($alertRange);
			        	$alertMax = max($alertRange);
			        	if ($alertMin == 101 && $alertMax == 1231) {
			        		$match['$or'] = [
			        			[
			        				'$and' => [
			        					['date.nd' => ['$gte' => $alertRange[0]]],
			        					['date.nd' => ['$lte' => $alertMax]],
			        				],
			        			],
			        			[
			        				'$and' => [
			        					['date.nd' => ['$gte' => $alertMin]],
			        					['date.nd' => ['$lte' => end($alertRange)]],
			        				],
			        			],
			        		];
			        	} else {
			        		$match['$and'] = [
		        				['date.nd' => ['$gte' => $alertMin]],
		        				['date.nd' => ['$lte' => $alertMax]],
			        		];
			        	}

				        $alerts = Yii::$app->mongodb->getCollection(BaseAlert::collectionName())->aggregate(['$match' => $match]);

						foreach ($alerts as $alert) {
			                $guestIds[] = new \MongoId($alert['assign_item']);
			            }
					}
			    }
	        }
	    }

	    foreach (Guest::find()->where(['_id' => ['$in' => $guestIds]])->all() as $guest) {
	    	$birthday = (int)Yii::$app->formatter->asDate($guest->birthday, 'php:nd');
	    	$howOldAreYou = $year - (int)Yii::$app->formatter->asDate($guest->birthday, 'php:Y');
	    	$result[$birthday][] = [
	    		'label' => Alert::t('app', 'Alert guest {guest_name}, birthday {guest_birthday}', [
	    			'guest_name' => $guest->name,
	    			'guest_birthday' => Yii::$app->formatter->asDate($guest->birthday, 'php:m-d'),
	    		]).' '.'<label class="label label-danger mark-label">'.Alert::t('app', '{year} year(s) old', ['year' => $howOldAreYou]).'</label>',
	    		'url' => Url::toRoute(['/guest/guest/update', 'pid' => $guest->pid, 'id' => (string)$guest->_id]),
	    		'options' => [
		    		'class' => $birthday == 229 && !$leap ? 'no-leap-alert' : '',
	    		],
	    	];
	    }

	    foreach ($result as $items) {
	    	if ($items != null) {
		    	$this->total += count($items);
		    	$this->items = array_merge($this->items, $items);
		    }
	    }
    }

    private function getAlertRange($daysAlert, $leap, $time)
    {
    	$dates = [];
    	$today = strtotime(Yii::$app->formatter->asDate($time, 'php:Y-m-d'));
    	$end = $today + (86400 * $daysAlert);

    	while ($today <= $end) {
        	$dates[] = (int)Yii::$app->formatter->asDate($today, 'php:nd');
        	$today = strtotime('+1 day', $today);
    	}

        if (!$leap && in_array(228, $dates) && $dates[$daysAlert] !== 228) {
        	$dates[] = 229;
        	asort($dates);
        	$dates = array_values($dates);
        }

        return $dates;
    }

    private function getGuestBirthdayAlertSettings($projectIds)
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

            if (isset($project['settings']['alert']['guest_birthday'])) {
                $result[$projectId] = $project['settings']['alert']['guest_birthday'];
            } else {
                $result[$projectId] = Yii::$app->getModule('alert')->getDefaultGuestBirthdayAlertDay();
            }
        }

        return $result;
    }
}
