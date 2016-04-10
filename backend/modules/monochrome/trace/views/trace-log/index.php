<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\monochrome\trace\models\TraceLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trace Logs';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="trace-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
        $columns = [
            ['class' => 'yii\grid\SerialColumn'],

            // '_id' => '_id',
            // 'level' => 'level',
            'log_time' => [
                'attribute' => 'log_time',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'log_time',
                    'options' => ['class' => 'form-control', 'readonly' => true],

                    //'language' => 'ru',
                    //'dateFormat' => 'yyyy-MM-dd',
                  ]),
                'format' => 'datetime',
            ],
            // 'category' => [
            //     'attribute' => 'category',
            //     'filter' => $filterCategory,
            // ],
            'prefix' => [
                'attribute' => 'prefix',
            ],
            'message' => 'message:html',

            // ['class' => 'yii\grid\ActionColumn'],
        ];

        if ($filterDropDownList != null) {
            $columns['prefix']['filter'] = $filterDropDownList;
        }
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>

</div>
