<?php

use yii\helpers\Html;
use common\modules\monochrome\taxonomy\Taxonomy;
use common\modules\monochrome\taxonomy\models\Type;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\taxonomy\models\Item */

$this->params['breadcrumbs'][] = ['label' => Taxonomy::t('app', 'Types'), 'url' => ['list']];
$this->title = Type::getTypeListByVendor(Yii::$app->user->getVendor())[$model->type]['name'] . ' - ' . Taxonomy::t('app', 'Update Item') . ' - ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Type::getTypeListByVendor(Yii::$app->user->getVendor())[$model->type]['name'] . ' - ' . Taxonomy::t('app', 'Items'), 'url' => ['index', 'type' => $model->type]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'type' => $type,
    ]) ?>

</div>
