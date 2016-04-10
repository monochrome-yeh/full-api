<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\monochrome\taxonomy\Taxonomy;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\taxonomy\models\Item */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    	// echo $form->field($model, 'type')->dropDownList($type, ['prompt' => Taxonomy::t('app', 'Please choose a type.')]);
    ?>

    <?= $form->field($model, 'name') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common/app', 'Create') : Yii::t('common/app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
