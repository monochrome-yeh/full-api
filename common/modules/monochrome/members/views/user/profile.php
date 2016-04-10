<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\bootstrap\ActiveForm;
use common\modules\monochrome\members\Members;

/* @var $this yii\web\View */
/* @var $user app\modules\monochrome\members\models\User */

$this->title = 'Update User: ' . ' ' . $user->_id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin([]); ?>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
	    <?= Alert::widget([
	        'options' => [
	            'class' => 'alert-success',
	        ],
	        'body' =>  Yii::$app->session->getFlash('success'),
	    ]); ?>
    <?php endif ?>

    <div class="user-form">
    	<div class="form-group">
			<?= $this->render('components/common', [
				'user' => $user,
				'form' => $form,
			]) ?>
			
			<?= $this->render('components/password', [
				'user' => $user,
				'form' => $form,
			]) ?>
		</div>
	    <div class="form-group">
	        <?= Html::submitButton($user->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
	            ['class' => $user->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
	            'data' => $user->isNewRecord ? []:[
	                'confirm' => Yii::t('common/app', 'Are you sure you want to update this item?'),
	            ],
	        ]) ?>
	    </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
