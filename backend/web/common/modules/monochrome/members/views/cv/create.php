<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\members\models\CVModel */

$this->title = Yii::t('app', 'Create Cvmodel');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cvmodels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cvmodel-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
