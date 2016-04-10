<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\User;
use common\modules\monochrome\members\models\Vendor;
use common\modules\monochrome\members\models\VendorUser;
use frontend\modules\project_management\monochrome\project\models\Project;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Members::t('app', 'Vendor Users');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'resizableColumns' => false,
        'filterRowOptions' => ['class' => 'visible-md visible-lg'],
        'floatHeader' => true,
        'responsiveWrap' => false,
        'dataProvider' => $searchModel->search(Yii::$app->request->get()),
        'filterModel' => $searchModel,
        'columns' => [
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
            [
                'attribute' => 'vid',
                'filter' => Vendor::getVendorList(),
                'content' => function($data) {
                    return Vendor::getVendorList()[$data->vid];
                }
            ],
            'account',
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
                    }
                ],
                'template' => '{update} {reset} {status}',
            ],
        ],
    ]); ?>

</div>
