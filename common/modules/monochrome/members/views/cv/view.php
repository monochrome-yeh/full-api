<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\members\models\CVModel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cvmodels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cvmodel-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'uid' => $uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'uid' => $uid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'email',
            'from',
            [
                'attribute' => 'tel',
                'value' => implode('，', (array)$model->tel),
            ],
            'age',
            [
                'attribute' => 'skills',
                'value' => implode('，', (array)$model->skills),
            ],
            'skill_details',
            'introduction',
            [
                'attribute' => 'portfolio',
                'value' => implode('，', (array)$model->portfolio),
            ],
            [
                'attribute' => 'experience',
                'value' => implode('，', (array)$model->experience),
            ],
        ],
    ]) ?>

</div>
