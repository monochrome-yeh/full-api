<p>貴公司 <?= $vendor_name ?> 您好：</p>
<p>已成功取消一筆<?= Yii::$app->formatter->asCurrency($price) ?>元，購買<?= $month ?>個月的加值紀錄。</p>
<p>調整前建案起訖日設定如下：</p>
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
<p>調整後建案起訖日設定如下：</p>
<p>
	<?php foreach ($afterSetProjects as $info): ?>
		<p>建案名稱：<?= $info['name'] ?></p>
		<?php foreach ($info['record'] as $record): ?>
			<p>起始日：<?= Yii::$app->formatter->asDate($record['begin_date']) ?></p>
			<p>到期日：<?= Yii::$app->formatter->asDate($record['end_date']) ?></p>
			<p>使用天數：<?= $record['range'] ?></p>
		<?php endforeach ?>
	<?php endforeach ?>
</p>
<p>目前總共的加值月數：<?= $monthQuota ?>個月。</p>
<p>目前使用的總天數：<?= $realUseDays ?>天。</p>
<p>目前剩餘的總天數：<?= $theRestOfDays ?>天。</p>
<p>調整原因說明如下：</p>
<p>如果您調整前所有建案的起迄日的總和天數小於或是等於您目前的加值天數，則沒有任何問題。</p>
<p>但若已大於您目前的加值天數，系統會自動設定所有建案的到期日為明天。</p>
<p>倘若將您所有建案的到期日設定為明天，您的使用天數仍然大於您目前的加值天數，系統會自動設定所有建案的到期日為今天(已到期)。</p>
<p>依據上述的狀況如果還有剩餘天數可以使用，麻煩通知您的剩餘天數該如何使用，以免您的權益受損。</p>
<p>如需加值、建案到期日調整或是其他服務敬請告知。</p>
