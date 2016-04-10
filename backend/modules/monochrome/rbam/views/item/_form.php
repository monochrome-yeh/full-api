<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\monochrome\rbam\RBAM;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $model app\modules\monochrome\rbam\models\Item */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if (Yii::$app->session->hasFlash('success')): ?>

    <?php endif ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'display_name') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'tag') ?>

	<?php if ($model->type == 1): ?>
	<div class="form-group">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title"><?= RBAM::t('app', 'Role') ?></h3>
            </div>
            <div class="panel-body">
                <?= Html::checkboxList('Item[roles]', $model->getChildren(), $model->getRoles(), ['item'=>function ($index, $label, $name, $checked, $value){
                    return '<div class="col-md-4">' . Html::checkbox($name, $checked, [
                       'value' => $value,
                       'label' => $label,
                    ]) . '</div>';
                }]); ?>
            </div>
        </div>
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title"><?= RBAM::t('app', 'Permission') ?></h3>
            </div>
            <div class="panel-body">
                <?= Html::checkboxList('Item[permissions]', $model->getChildren(), $model->getPermissions(), ['item'=>function ($index, $label, $name, $checked, $value){
                    return '<div class="col-md-4">' . Html::checkbox($name, $checked, [
                       'value' => $value,
                       'label' => $label,
                    ]) . '</div>';
                }]); ?>
            </div>
        </div>
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><?= RBAM::t('app', 'Field') ?></h3>
            </div>
            <div class="panel-body">
                <?= Html::checkboxList('Item[fields]', $model->getChildren(), $model->getFields(), ['item'=>function ($index, $label, $name, $checked, $value){
                    return '<div class="col-md-4">' . Html::checkbox($name, $checked, [
                       'value' => $value,
                       'label' => $label,
                    ]) . '</div>';
                }]); ?>
            </div>
        </div>        
	</div>
	<?php endif ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common/app', 'Create') : Yii::t('common/app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
