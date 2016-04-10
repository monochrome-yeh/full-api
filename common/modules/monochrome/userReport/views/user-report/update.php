<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\userReport\models\UserReport */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'User Report',
]) . ' ' . $model->_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->_id, 'url' => ['view', 'id' => (string)$model->_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="user-report-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
