<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\modules\monochrome\members\Members;

/* @var $this yii\web\View */
/* @var $user app\modules\monochrome\members\models\User */

$this->title = Members::t('app', 'Create Vendor User');
$this->params['breadcrumbs'][] = ['label' => Members::t('app', 'Vendor Users'), 'url' => ['index', 'vid' => $user->vid]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="vendor-user-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(); ?>

    <div class="user-form">
    	<div class="form-group">
		    <?= $this->render('components/common', [
		        'user' => $user,
		        'form' => $form,
		    ]) ?>

		    <?= $form->field($user, 'pid')->checkBoxList(array_merge($projects['active'], $projects['unActive']), ['prompt' => Members::t('app', 'Please assign a project to user.')]) ?>

		    <?= $form->field($user, 'role')->dropDownList(Yii::$app->getModule('members')->getDropDownListForCustomRoles(), ['prompt' => Members::t('app', 'Please choose a role')]) ?>

			<?= Html::submitButton(yii::t('common/app', 'Create'), ['class' => 'btn btn-success']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
