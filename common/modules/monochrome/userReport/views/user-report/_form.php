<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\monochrome\userReport\models\UserReport;
/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\userReport\models\UserReport */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-report-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList(UserReport::getTypeList()) ?>

    <?= $form->field($model, 'desc')->textArea() ?>

	<?php if ($this->userCan('update_solved')) : ?>
    <?= $form->field($model, 'is_solved')->checkBox() ?>
	<?php endif ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common/app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
