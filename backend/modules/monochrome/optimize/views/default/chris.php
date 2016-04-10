<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Chris';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(
	Yii::getAlias('@web').'/js/chris.js', ['depends' => [\yii\web\JqueryAsset::className()], 'position' => View::POS_BEGIN]
);

?>

<div class="default-chris">

	<?php $form = ActiveForm::begin(['id' => 'chris-form']); ?>

		<p>Click below button to generate random data.</p>

		<?= $form->field($model, 'vendors')->dropDownList([], ['id' => 'vendors']); ?>
		<?= $form->field($model, 'number')->input('number', ['id' => 'number', 'step' => 1]); ?>

		<button class="btn btn-primary" onclick="createData();return false;">Generate Data</button>

		<p id="data" class="hidden"></p>

	<?php ActiveForm::end(); ?>

</div>
