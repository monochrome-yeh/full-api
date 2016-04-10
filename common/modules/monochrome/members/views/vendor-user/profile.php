<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\bootstrap\ActiveForm;
use common\modules\monochrome\members\Members;

/* @var $this yii\web\View */
/* @var $user app\modules\monochrome\members\models\User */

$this->title = Members::t('app', 'Your Account');
$this->params['breadcrumbs'][] = 'Profile';
?>

<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="user-form">
    	<div class="form-group">
			<?= $this->render('components/common', [
				'fu' => $fu,
				'user' => $user,
				'form' => $form,
			]) ?>

			<?= $this->render('components/password', [
				'user' => $user,
				'form' => $form,
			]) ?>

			<?= $form->field($user, 'phone'); ?>

			<div class="ibox">
				<div class="ibox-title"><?= Yii::t('common/app', 'Settings')?></div>	
			    <div class="ibox-content">
					<?= $this->render('components/settings', [
				        'user' => $user,
				        'form' => $form,
				    ]) ?>		    	
			    </div>
			</div>	
			<?= Html::submitButton(Yii::t('common/app', 'Update'), [
				'class' => 'btn btn-primary',
				'data' => [
					'confirm' => Yii::t('common/app', 'Are you sure you want to update this item?'),
					'method' => 'post',
				],
			]) ?>
		</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
