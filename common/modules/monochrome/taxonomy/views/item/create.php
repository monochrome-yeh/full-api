<?php

use yii\helpers\Html;
use common\modules\monochrome\taxonomy\Taxonomy;
use common\modules\monochrome\taxonomy\models\Type;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\taxonomy\models\Item */

$this->params['breadcrumbs'][] = ['label' => Taxonomy::t('app', 'Types'), 'url' => ['list']];
$this->title = Type::getTypeListByVendor(Yii::$app->user->getVendor())[Yii::$app->request->get('type')]['name'] . ' - ' . Taxonomy::t('app', 'Create Item');
$this->params['breadcrumbs'][] = ['label' => Type::getTypeListByVendor(Yii::$app->user->getVendor())[Yii::$app->request->get('type')]['name'] . ' - ' . Taxonomy::t('app', 'Items'), 'url' => ['index', 'type' => Yii::$app->request->get('type')]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    	<?= Taxonomy::t('app', 'Set Ad Meida Or Ad Media Request Items') ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
        'type' => $type,
    ]) ?>

</div>
