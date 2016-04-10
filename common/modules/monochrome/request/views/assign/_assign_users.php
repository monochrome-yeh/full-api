<?php

use yii\helpers\Html;

$this->registerCss(
"
.assign-user {
	margin: 0 0 10px 0;
}
"
);

?>

<?php foreach($assignUsers as $id => $name): ?>
	<div class="checkbox assign-user i-checks">
		<span class="icheckbox_square-aero<?= isset($doneUsers[$id]) ? ' checked' : '' ?>">
			<input type="checkbox">
		</span>
		<span><?= Html::encode($name) ?></span>
	</div>
<?php endforeach ?>
