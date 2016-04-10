<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

if ($singleDateSelect) {
$this->registerJs(
"
    var date1 = {},
        date2 = {};

    var startDate;
    var endDate

    $('.dateSelector').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        maxDate: '{$max_date}',
    });

    $('.dateSelector2').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        maxDate: '{$max_date}',
    });

", $this::POS_END);
}
?>

<?php $form = ActiveForm::begin([
    'method' => isset($method) ? $method : 'post',
]); ?>
<h4><?= Yii::t('common/app', 'select caculate range date.'); ?></h4>

<?= $this->render('date_widget', ['buttons' => $buttons, 'week_start' => $week_start, 'form' => $form, 'max_date' => $max_date]) ?>

<hr>
<div class="form-group">
<?= $form->field($dateModel, 'fromDate')->input('text',['class' => 'dateSelector form-control', 'readonly' => !$singleDateSelect]) ?>
<?= $form->field($dateModel, 'toDate')->input('text',['class' => 'dateSelector2 form-control', 'readonly' => !$singleDateSelect]) ?>
</div>
<div class="form-group">
    <?= Html::submitButton(Yii::t('common/app', 'Submit'), ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
