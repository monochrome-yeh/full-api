<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\bootstrap\ActiveForm;
use common\modules\monochrome\members\Members;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = Members::t('app', 'Forget Password');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="reset-password">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Members::t('app', 'Please fill out the following fields to get password:'); ?>
    </p>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'account')->label(Members::t('app', 'Account')) ?>

    <?= $form->field($model, 'email') ?>

    <div class="form-group">
        <?= Html::submitButton(Members::t('app', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'reset-password-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
