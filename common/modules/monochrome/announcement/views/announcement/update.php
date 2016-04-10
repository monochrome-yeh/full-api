<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\announcement\models\Announcement */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Announcement',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Announcements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => (string)$model->_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="announcement-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
