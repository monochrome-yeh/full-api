<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\modules\monochrome\rbam\RBAM;

/* @var $this yii\web\View */
/* @var $model app\modules\monochrome\rbam\models\Item */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => RBAM::t('app', $type_name), 'url' => ['index', 'id' => $model->type]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'_id',
            'name',
            'type',
            'description',
            'tag',
        ],
    ]) ?>

</div>
