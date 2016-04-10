<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\monochrome\members\Members;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Members::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // '_id',
            'username',
            [
                'attribute' => 'account',
                'header' => Members::t('app', 'Account'),
                'value' => function ($model, $key, $index, $column) {
                    return $model['login']['normal']['account'];
                }
            ],
            // 'detail',
            // 'tags',
            // 'agree_terms',
            // 'status',
            // 'created_at',
            // 'updated_at',
            // 'auth_key',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'reset' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-refresh"></span>', $url, [
                            'title' => Yii::t('common/app', 'Reset Password'),
                            'data-confirm' => Yii::t('common/app', 'Are you sure you want to reset password?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    },
                    'status' => function ($url, $model, $key) {
                        return $model->getUserStatusAction((string)$model->_id, $model->status);
                    },
                    'cv' => function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-list-alt"></i>', Url::toRoute(['/members/cv/update', 'uid' => (string)$model->_id]), ['class' => 'btn btn-info'
                        ]);
                    }
                ],
                'template' => '{update} {reset} {cv} {status} {delete}',
            ],
        ],
    ]); ?>

</div>
