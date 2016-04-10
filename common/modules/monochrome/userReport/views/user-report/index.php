<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\monochrome\userReport\models\UserReport;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\monochrome\userReport\models\SearchUserReport */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'User Reports');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-report-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create User Report'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'uid',
            ],
            [
                'attribute' => 'type',
                'filter' => UserReport::getTypeList(),
                'content' => function($data) {
                    return UserReport::getTypeList()[$data->type];
                }
            ],
            'desc',
            'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}'
            ],
        ],
    ]); ?>

</div>
