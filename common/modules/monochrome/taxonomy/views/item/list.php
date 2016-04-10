<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\modules\monochrome\taxonomy\Taxonomy;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Taxonomy::t('app', 'Types');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="taxonomy-item-list">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // '_id',
            // 'fields',
            // 'unique_name',

            'name',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'create_item' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-plus"></span>', ["index", 'type' => $model->unique_name], [
                            'title' => Taxonomy::t('app', 'Create Item'),
                            'class' => 'btn btn-primary',
                        ]);
                    },
                ],
                'template' => '{create_item}',
            ],
        ],
    ]); ?>

</div>
