<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\modules\monochrome\taxonomy\Taxonomy;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\taxonomy\models\Item */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Taxonomy::t('app', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'vid',
            'type',
            'name',
        ],
    ]) ?>

</div>
