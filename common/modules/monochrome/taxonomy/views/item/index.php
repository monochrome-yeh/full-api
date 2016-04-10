<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\modules\monochrome\taxonomy\Taxonomy;
use common\modules\monochrome\taxonomy\models\Type;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label' => Taxonomy::t('app', 'Types'), 'url' => ['list']];
$this->title = Type::getTypeListByVendor(Yii::$app->user->getVendor())[Yii::$app->request->get('type')]['name'] . ' - ' . Taxonomy::t('app', 'Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Taxonomy::t('app', 'Create') . Type::getTypeListByVendor(Yii::$app->user->getVendor())[Yii::$app->request->get('type')]['name'] . Taxonomy::t('app', 'Item'), ['create', 'type' => Yii::$app->request->get('type')], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => false,        
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // '_id',
            // 'vid',
            // 'type',

            'name',

            [
                'class' => 'yii\grid\ActionColumn',
                
                'template' => '{update}',
            ],
        ],
    ]); ?>

</div>
