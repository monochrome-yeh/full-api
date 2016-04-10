<p>貴公司 <?= $vendor_name ?> 您好：</p>
<p>已成功取消一筆<?= Yii::$app->formatter->asCurrency($price) ?>元，購買<?= $month ?>個月的加值紀錄。</p>
<p>目前建案起訖日設定如下：</p>
<p>
	<?php foreach ($beforeSetProjects as $info): ?>
		<p>建案名稱：<?= $info['name'] ?></p>
		<?php foreach ($info['record'] as $record): ?>
			<p>起始日：<?= Yii::$app->formatter->asDate($record['begin_date']) ?></p>
			<p>到期日：<?= Yii::$app->formatter->asDate($record['end_date']) ?></p>
			<p>使用天數：<?= $record['range'] ?></p>
		<?php endforeach ?>
	<?php endforeach ?>
</p>
<p>目前總共的加值月數：<?= $monthQuota ?>個月。</p>
<p>目前使用的總天數：<?= $useDays ?>天。</p>
<p>目前剩餘的總天數：<?= $theRestOfDays ?>天。</p>
<p>如需加值、建案到期日調整或是其他服務敬請告知。</p>
