<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\members\models\CVModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cvmodel-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'from') ?>

    <?= $form->field($model, 'tel') ?>

    <?= $form->field($model, 'age') ?>

    <?= $form->field($model, 'skills') ?>

    <?= $form->field($model, 'skill_details') ?>

    <?= $form->field($model, 'introduction') ?>

    <?= $form->field($model, 'portfolio') ?>

    <?= $form->field($model, 'experience') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
