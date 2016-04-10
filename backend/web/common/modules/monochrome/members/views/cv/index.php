<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Cvmodels');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cvmodel-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Cvmodel'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            '_id',
            'name',
            'from',
            'tel',
            'age',
            // 'skills',
            // 'skill_details',
            // 'introduction',
            // 'portfolio',
            // 'experience',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
