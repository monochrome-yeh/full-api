<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\monochrome\trace\models\TraceLog */

$this->title = 'Update Trace Log: ' . ' ' . $model->_id;
$this->params['breadcrumbs'][] = ['label' => 'Trace Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->_id, 'url' => ['view', 'id' => (string)$model->_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trace-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
