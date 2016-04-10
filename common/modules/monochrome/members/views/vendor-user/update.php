<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\bootstrap\ActiveForm;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\VendorUser;

/* @var $this yii\web\View */
/* @var $user app\modules\monochrome\members\models\User */

$this->title = Members::t('app', 'Update Vendor User');
$this->params['breadcrumbs'][] = ['label' => Members::t('app', 'Vendor Users'), 'url' => ['index', 'vid' => $user->vid]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="vendor-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin([]); ?>

    <div class="user-form">
    	<div class="form-group">
			<?= $this->render('components/common', [
				'user' => $user,
				'form' => $form,
			]) ?>

			<?= $form->field($user, 'pid')->checkBoxList(array_merge($projects['active'], $projects['unActive']), ['prompt' => Members::t('app', 'Please assign a project to user.')]) ?>

			<div class="panel panel-warning">
			    <div class="panel-heading">
			        <h3 class="panel-title"><?= Members::t('app', 'Disable Projects') ?></h3>
			    </div>
			    <div class="panel-body">
			    	<?php foreach ($projects['unActive'] as $project): ?>
			    		<div class="col-md-4"><?= $project ?></div>
					<?php endforeach; ?>
			    </div>
			</div>

		    <?= $form->field($user, 'role')->dropDownList(Yii::$app->getModule('members')->getDropDownListForCustomRoles(), ['prompt' => Members::t('app', 'Please choose a role')]) ?>

		    <?= $this->render('components/status', [
		        'user' => $user,
		        'form' => $form,
		    ]) ?>
		</div>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('common/app', 'Update'), [
				'class' => 'btn btn-primary',
				'data' => [
					'confirm' => Yii::t('common/app', 'Are you sure you want to update this item?'),
					'method' => 'post',
				],
			]) ?>

	        <?= Html::a(Yii::t('common/app', 'Reset Password'), ['reset', 'id' => $user->getId()], [
	        	'class' => 'btn btn-warning',
	        	'data' => [
					'confirm' => Yii::t('common/app', 'Are you sure you want to reset password?'),
					'method' => 'post',
				],
				'data-pjax' => '0',
	        ])?>

	        <?= VendorUser::getUserStatusAction((string)$user->getId(), $user->status, false); ?>
		</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
