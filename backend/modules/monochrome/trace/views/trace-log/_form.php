<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\monochrome\trace\models\TraceLog;

/* @var $this yii\web\View */
/* @var $model backend\modules\monochrome\trace\models\TraceLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trace-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'category')->dropDownList(TraceLog::getCategory()) ?>

    <?= $form->field($model, 'message')->textArea() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
