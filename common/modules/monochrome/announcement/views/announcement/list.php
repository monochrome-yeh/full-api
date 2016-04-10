<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\monochrome\announcement\Announcement;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\monochrome\announcement\models\SearchAnnouncement */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Announcement::t('app', 'Announcement');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="announcement-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            'content',
            'created_at:date',
        ],
    ]); ?>

</div>
