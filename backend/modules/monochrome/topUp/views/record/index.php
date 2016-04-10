<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use backend\modules\monochrome\topUp\TopUp;
use common\modules\monochrome\members\models\Vendor;
use common\modules\monochrome\members\models\AdminUser;
use frontend\modules\project_management\monochrome\project\models\ProjectSuper;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\monochrome\topUp\models\ProjectTopUpLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = TopUp::t('app', 'Project Top Up Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-top-up-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // '_id',
            [
                'attribute' => 'vid',
                'filter' => Vendor::getVendorList(),
                'value' => function ($model, $key, $index, $column) {
                    return Vendor::getVendorList()[$model->vid];
                }
            ],
            [
                'attribute' => 'pid',
                'filter' => ProjectSuper::getProjectList(),
                'value' => function ($model, $key, $index, $column) {
                    return ProjectSuper::getProjectList()[$model->pid];
                }
            ],
            [
                'attribute' => 'creator',
                'filter' => AdminUser::getAdminUserList(),
                'value' => function ($model, $key, $index, $column) {
                    return AdminUser::getAdminUserList()[$model->creator];
                }
            ],
            [
                'format' => 'datetime',
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
            ],
            'log_content',
            // 'updated_at',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
