<?php

use yii\helpers\Html;
use backend\modules\monochrome\rbam\RBAM;

/* @var $this yii\web\View */
/* @var $model app\modules\monochrome\rbam\models\Item */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => RBAM::t('app', $type_name),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Items'), 'url' => ["index", 'id' => $model->type]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
