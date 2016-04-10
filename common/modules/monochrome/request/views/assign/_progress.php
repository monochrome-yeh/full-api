<?php

use yii\bootstrap\Progress;
use common\modules\monochrome\request\Request;

if ($value > 0) {
	$percent = Yii::$app->formatter->asPercent($value, 2);
	echo '<small>'.$percent.'</small>';
	echo Progress::widget([
		'percent' => str_replace('%', '', $percent),
		'options' => [
			'class' => 'progress-mini',
		],
		'barOptions' => [
			'class' => 'progress-bar-success'
		],
	]);
}

?>
