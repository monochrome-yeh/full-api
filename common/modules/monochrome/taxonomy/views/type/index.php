<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\monochrome\taxonomy\Taxonomy;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Taxonomy::t('app', 'Types');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="taxonomy-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Taxonomy::t('app', 'Create{modelClass}', ['modelClass' =>  Taxonomy::t('app', 'Type')]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // '_id',
            // 'fields',

            'unique_name',
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
