<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\monochrome\taxonomy\Taxonomy;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\taxonomy\models\Type */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="taxonomy-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'unique_name') ?>

    <?= $form->field($model, 'name') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
