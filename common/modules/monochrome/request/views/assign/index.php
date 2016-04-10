<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use kartik\grid\GridView;
use common\modules\monochrome\request\models\ToDoList;
use common\modules\monochrome\request\controllers\ToDoListController;
use common\modules\monochrome\request\Request;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\monochrome\request\models\ToDoListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Request::t('app', 'To Assign Lists');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assign-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Request::t('app', 'Create To Assign List'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'pjax' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'striped' => false,
        'columns' => [
            // '_id',
            // [
            //     'attribute' => 'deadline',
            //     'filter' => DatePicker::widget([
            //         'model' => $searchModel,
            //         'attribute' => 'deadline',
            //         'options' => ['class' => 'form-control', 'readonly' => true],
            //         'clientOptions' => [
            //             'changeYear' => true,
            //             'changeMonth' => true
            //         ],
            //         //'language' => 'ru',
            //         //'dateFormat' => 'yyyy-MM-dd',
            //     ]),
            //     'contentOptions' => [
            //         'style' => 'width:14%;',
            //     ],
            //     'format' => ['wDateNoYear'],
            // ],
            [
                'attribute' => 'created_at',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'options' => ['class' => 'form-control', 'readonly' => true],

                    //'language' => 'ru',
                    //'dateFormat' => 'yyyy-MM-dd',
                ]),
                'contentOptions' => [
                    'style' => 'width:14%;',
                ],
                'format' => ['wDateNoYear'],
            ],
            'content',
            [
                'attribute' => 'status',
                'format' => 'html',
                'filter' => ToDoList::getStatusOptions(),
                'value' => function ($model, $key, $index, $column) {
                    return ToDoListController::getStatusForView()[$model->status];
                }
            ],
            [
                'attribute' => 'progress',
                'format' => 'html',
                'contentOptions' => [
                    'style' => 'width:20%;',
                ],
                'value' => function ($model, $key, $index, $column) {
                    $assignUsersNumber = count($model->assign_users);

                    return $this->render('_progress', [
                        'value' => $assignUsersNumber > 0 ? count($model->done_users)/$assignUsersNumber : 0,
                    ]);
                }
            ],
            // 'assigner',
            // 'assign_users',
            // 'done_users',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        if ($model->status === ToDoList::STATUS_NOT_YET) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'class' => 'btn btn-default',
                                'title' => Yii::t('yii', 'Update'),
                                'aria-label' => Yii::t('yii', 'Update'),
                                'data-pjax' => '0',
                            ]);
                        }

                        return '';
                    },
                ],
                'template' => '{view} {delete}',
            ],
        ],
    ]); ?>

</div>
