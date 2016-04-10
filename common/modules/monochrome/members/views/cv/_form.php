<?php

use yii\widgets\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\members\models\CVModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cvmodel-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'from') ?>
    
    <div class="form-group">
        <?= Html::activeLabel($model, 'tel'); ?>

        <?php foreach ((array)$model->tel as $_value) : ?>
            <?= Html::input('text', 'CVModel[tel][]', $_value, ['class' => 'form-control']) ?>
        <?php endforeach ?>

        <?= Html::input('text', 'CVModel[tel][]', '', ['class' => 'form-control', 'id' => 'cvmodel-tel']) ?>
    </div>

    <?= $form->field($model, 'age') ?>




    <?= $form->field($model, 'skill_details')->textarea(['rows' => '20']) ?>

    <?= $form->field($model, 'introduction')->textarea(['rows' => '6']) ?>

    <div class="form-group">
        <?= Html::activeLabel($model, 'portfolio'); ?>

        <?php foreach ((array)$model->portfolio as $_value) : ?>
            <?= Html::input('text', 'CVModel[portfolio][]', $_value, ['class' => 'form-control']) ?>
        <?php endforeach ?>

        <?= Html::input('text', 'CVModel[portfolio][]', '', ['class' => 'form-control', 'id' => 'cvmodel-portfolio']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
