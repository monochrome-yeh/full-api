<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\modules\monochrome\members\Members;

/* @var $this yii\web\View */
/* @var $user app\modules\monochrome\members\models\User */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => 'index'];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(); ?>

    <div class="user-form">
    	<div class="form-group">
		    <?= $this->render('components/common', [
		        'user' => $user,
		        'form' => $form,
		    ]) ?>

			<?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
