<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\modules\monochrome\members\Members;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = Yii::t('common/app', 'Login');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('https://www.google.com/recaptcha/api.js',['position' => $this::POS_HEAD]);
$this->registerJs("
    var RecaptchaOptions = {
       lang : 'zh-TW',
    };
$(window).resize(function() {
    var recaptcha = $('.g-recaptcha');
    if(recaptcha.css('margin') == '1px') {
        var newScaleFactor = recaptcha.parent().innerWidth() / 304;
        recaptcha.css('transform', 'scale(' + newScaleFactor + ')');
        recaptcha.css('transform-origin', '0 0');
    }
    else {
        recaptcha.css('transform', 'scale(1)');
        recaptcha.css('transform-origin', '0 0');
    }
});    
", $this::POS_END);

$this->registerCss("
@media(max-width: 390px) {
    .g-recaptcha {
        margin: 1px;
    }
}
");
?>
<div class="site-login">
    <h1><?= Html::encode($vendor_name.' - '.$this->title) ?></h1>

    <p>
        <?= Members::t('app', 'Please fill out the following fields to login:'); ?>
    </p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'vid',['template' => '{input}'])->hiddenInput() ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?php if (Yii::$app->getModule('members')->google['recaptcha']['enable']) : ?>
    <div class="g-recaptcha" data-sitekey="6Lep5wkTAAAAAAxHKzVKZo3FKb_9yKPhFNaddxVM" data-size="30%"></div>
    <?= $form->field($model, 'reCAPTCHA', ['template' => "{error}"]) ?>
    <?php endif ?>
    <div class="col-lg-offset-1 col-lg-11">
    <?= $form->field($model, 'rememberMe', [
        'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ])->checkbox() ?>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton(Yii::t('common/app', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            <?php if (!empty($vid)): ?>
                <?= Html::a(Members::t('app', 'Forget Password'), Url::toRoute(["/members/vendor-user/request/{$vid}"]), ['class' => 'btn btn-warning']); ?>
            <?php endif ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
