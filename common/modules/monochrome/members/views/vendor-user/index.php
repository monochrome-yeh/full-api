<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\DetailView;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\User;
use common\modules\monochrome\members\models\VendorUser;
use frontend\modules\project_management\monochrome\project\models\Project;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Members::t('app', 'Vendor Users');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-index">

    <h1><?= !empty(Project::getProjectNameById($vid) ? Project::getProjectNameById($vid) .'&nbsp;-&nbsp;' : '' ) ?><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('superadmin') || Yii::$app->user->can('admin')) : ?> 
        <p>
            <?= Html::a(Members::t('app', 'Create Vendor User'), ['create', 'vid' => $vid], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?= GridView::widget([
        'resizableColumns' => false,
        'filterRowOptions' => ['class' => 'visible-md visible-lg'],
        'floatHeader' => true,
        'responsiveWrap' => false,
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => '\kartik\grid\ExpandRowColumn',
                'enableRowClick' => true,
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    $projects = VendorUser::getVendorUserProjects($model->pid);
                    if ($projects != null) {
                        return DetailView::widget([
                            'model' => $projects,
                            'template' => '<tr><th class="col-xs-2">{label}</th><td class="col-xs-10">{value}</td></tr>',
                            'attributes' => [
                                [
                                    'label' => Members::t('app', 'Project'),
                                    'value' => implode('，', $projects),
                                ],
                            ],
                        ]);
                    } else {
                        return '<div class="alert alert-warning">'.Members::t('app', 'Not Set Project').'</div>';
                    }
                }
            ],

            // '_id',
            // 'tags',
            // 'detail',
            // 'auth_key',
            // 'agree_terms',
            // 'created_at',
            // 'updated_at',

            // [
            //     'attribute' => 'login',
            //     'header' => Members::t('app', 'Account'),
            //     'value' => function ($model, $key, $index, $column) {
            //         return $model['login']['vendor']['account'];
            //     }
            // ],
            // [
            //     'attribute' => 'login',
            //     'header' => 'Email',
            //     'value' => function ($model, $key, $index, $column) {
            //         return $model['login']['vendor']['email'];
            //     }
            // ],
            'username',
            [
                'attribute' => 'role',
                'format' => 'html',
                'label' => Members::t('app', 'Role'),
                'value' => function ($model, $key, $index, $column) {
                    $dropDownList = Yii::$app->getModule('members')->getDropDownListForCustomRoles();
                    if (isset($dropDownList[$model->role])) {
                        return $dropDownList[$model->role];
                    } else {
                        return '<span class="not-set">'.Members::t('app', 'Not Set Role').'</span>';
                    }
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->login_fail > 3) {
                        return Members::t('app', 'Disable Temporarily');
                    } else {
                        return $model->getUserStatus()[$model->status];
                    }

                    return '<span class="not-set">'.Members::t('app', 'Not Set Status').'</span>';
                },
                'visible' => (Yii::$app->user->can('superadmin') || Yii::$app->user->can('admin')),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                
                'buttons' => [
                    'reset' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-refresh"></span>', $url, [
                            'class' => 'btn btn-default',
                            'title' => Yii::t('common/app', 'Reset Password'),
                            'data-confirm' => Yii::t('common/app', 'Are you sure you want to reset password?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    },
                    'status' => function ($url, $model, $key) {
                        return $model->getUserStatusAction((string)$model->_id, $model->status);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'class' => 'btn btn-danger',
                            'title' => Yii::t('common/app', 'Delete'),
                            'data-confirm' => Yii::t('common/app', 'Are you sure you want to delete?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    },                    
                ],
                'template' => '{update} {reset} {status} {delete}',
                'visible' => (Yii::$app->user->can('superadmin') || Yii::$app->user->can('admin')),
            ],
        ],
        'export' => false,
    ]); ?>

</div>
