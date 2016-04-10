<?php

use yii\helpers\Html;
use common\modules\monochrome\request\Request;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\toDoList\models\ToDoList */

$this->title = Request::t('app', 'View To Assign List');
$this->params['breadcrumbs'][] = ['label' => Request::t('app', 'To Assign Lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assign-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_detail_info', ['model' => $model, 'showDeadline' => true]) ?>
    <?= $this->render('_detail_info', ['model' => $model]) ?>

</div>
