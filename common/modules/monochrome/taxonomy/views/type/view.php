<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\modules\monochrome\taxonomy\Taxonomy;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\taxonomy\models\Type */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Taxonomy::t('app', 'Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taxonomy-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
        ],
    ]) ?>

</div>
