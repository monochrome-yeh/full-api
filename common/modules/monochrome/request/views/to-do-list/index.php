<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\jui\DatePicker;
use kartik\grid\GridView;
use yii\bootstrap\Progress;
use common\modules\monochrome\request\models\ToDoList;
use common\modules\monochrome\request\controllers\ToDoListController;
use common\modules\monochrome\request\Request;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\monochrome\request\models\ToDoListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJs(
"
$('.to-do-check').on('ifChecked', function(event) {
    var checkId = this.id;
    var thisItem = $(this);

    if (confirm('".Request::t('app', 'Are you sure you have done this to do list?')."')) {
        $.ajax({
            type: 'POST',
            url: '".Url::toRoute(['/request/to-do-list/done'])."' + '/' + checkId,
        });
    } else {
        thisItem.iCheck('check');
        setTimeout(function() {
            thisItem.iCheck('uncheck');
        }, 100);
    }
});
"
, View::POS_READY, 'to-do-list-js');

$this->title = Request::t('app', 'To Do Lists');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="to-do-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
        $percent = 0;
        if (($totalCount = ToDoList::getTotalAssignToDoList()) > 0 && ($doneCount = ToDoList::getTotalDoneToDoList()) > 0) {
            $percent = $doneCount/$totalCount;
        }
    ?>
    <div class="ibox">
        <div class="ibox-content">
            <div>
                <span><?= Request::t('app', 'Overall Progress')?></span>
                <span class="pull-right"><?= Yii::$app->formatter->asPercent($percent, 2) ?></span>
            </div>
            <div>
                <?php if ($percent > 0): ?>
                    <?= Progress::widget([
                        // 'label' => $percent,
                        'percent' => $percent*100,
                        'barOptions' => ['class' => 'progress-bar-success'],
                    ]); ?>
                <?php else: ?>
                    <?= Progress::widget([
                        // 'label' => 0,
                        'percent' => $percent,
                        'barOptions' => ['class' => 'progress-bar-success'],
                    ]); ?>
                <?php endif ?>
            </div>
        </div>
    </div>
    <?= GridView::widget([
        'pjax' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'striped' => false,
        'columns' => [
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
                'class' => 'yii\grid\CheckboxColumn',
                'multiple' => false,
                'name' => 'toDoCheck',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    $result = ['id' => (string)$model->_id, 'class' => 'to-do-check'];

                    if ($model->getUserStatus() === ToDoList::STATUS_DONE) {
                        $result = array_merge($result, [
                            'disabled' => 'disabled',
                            'checked' => 'checked',
                        ]);
                    }

                    return $result;
                }
            ],
            [
                'attribute' => 'assigner',
                'filter' => ToDoList::getAssigners(),
                'value' => function ($model, $key, $index, $column) {
                    return isset(ToDoList::getAssigners()[$model->assigner]) ? ToDoList::getAssigners()[$model->assigner] : '<span class="not-set">'.Request::t('app', '(Not Set)').'<span>';
                }
            ],
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
                'attribute' => 'userStatus',
                'format' => 'html',
                'filter' => ToDoList::getStatusOptions(),
                'value' => function ($model, $key, $index, $column) {
                    return ToDoListController::getStatusForView()[$model->getUserStatus()];
                }
            ],

            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'buttons' => [
            //         'done' => function ($url, $model, $key) {
            //             if ($model->getUserStatus() === ToDoList::STATUS_NOT_YET) {
            //                 return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
            //                     'class' => 'btn btn-default',
            //                     'title' => Request::t('app', 'To Do List Done'),
            //                     'data-confirm' => Request::t('app', 'Are you sure you have done this to do list?'),
            //                     'data-method' => 'post',
            //                 ]);
            //             }

            //             return '';
            //         },
            //     ],
            //     'template' => '{done}',
            // ],
        ],
    ]); ?>

</div>
