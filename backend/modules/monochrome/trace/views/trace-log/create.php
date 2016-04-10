<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\monochrome\trace\models\TraceLog */

$this->title = 'Create Trace Log';
$this->params['breadcrumbs'][] = ['label' => 'Trace Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trace-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
