<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\bootstrap\ActiveForm;
use common\modules\monochrome\members\Members;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = Members::t('app', 'Reset Password');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="reset-password">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Members::t('app', 'Please fill out the following fields to get password:'); ?>
    </p>

    <?php $form = ActiveForm::begin(); ?>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget([
            'options' => [
                'class' => 'alert-success',
            ],
            'body' =>  Yii::$app->session->getFlash('success'),
        ]); ?>
    <?php endif ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'password_repeat')->passwordInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Members::t('app', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'reset-password-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
