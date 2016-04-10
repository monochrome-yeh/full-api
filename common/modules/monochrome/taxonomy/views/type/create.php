<?php

use yii\helpers\Html;
use common\modules\monochrome\taxonomy\Taxonomy;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\taxonomy\models\Type */

$this->title = Taxonomy::t('app', 'Create{modelClass}', ['modelClass' => Taxonomy::t('app', 'Type')]);
$this->params['breadcrumbs'][] = ['label' => Taxonomy::t('app', 'Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taxonomy-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
