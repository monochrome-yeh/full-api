<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\modules\monochrome\members\models\VendorUser;
use common\modules\monochrome\request\models\ToDoList;
use common\modules\monochrome\request\Request;

$assignUsers = VendorUser::getVendorUsers($model->assign_users);
$doneUsers = array_intersect_key($assignUsers, array_flip($model->done_users));

?>

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Html::encode(Request::t('app', 'To Assign List Info')) ?></h3>
    </div>

    <div class="panel-body">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'detail-view table'],
            'template' => '<tr><th class="alert-default">{label}</th><td>{value}</td></tr>',
            'attributes' => [
                // '_id',
                // 'assigner',
                // [
                //     'attribute' => 'deadline',
                //     'format' => 'wDateNoYear',
                //     'visible' => $showDeadline,
                // ],
                [
                    'attribute' => 'assign_users',
                    'label' => Request::t('app', 'Assign Users Progress'),
                    'format' => 'html',
                    'value' => $this->render('_assign_users', [
                        'assignUsers' => $assignUsers,
                        'doneUsers' => $doneUsers,
                    ]),
                ],
                // [
                //     'attribute' => 'assign_users',
                //     'value' => implode('，', $assignUsers),
                // ],
                // [
                //     'attribute' => 'done_users',
                //     'format' => 'html',
                //     'value' => $doneUsers != null ? implode('，', $doneUsers) : '<span class="not-set">'.Request::t('app', '(Not Set Done Users)').'<span>',
                // ],
                'content',
                [
                    'attribute' => 'status',
                    'value' => ToDoList::getStatusOptions()[$model->status],
                ],
                'created_at:wDateNoYear',
                // 'updated_at',
            ],
        ])?>
    </div>
</div>
