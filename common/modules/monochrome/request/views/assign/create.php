<?php

use yii\helpers\Html;
use common\modules\monochrome\request\Request;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\request\models\ToDoList */

$this->title = Request::t('app', 'Create To Assign List');
$this->params['breadcrumbs'][] = ['label' => Request::t('app', 'To Assign Lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assign-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'projectList' => $projectList,
    ]) ?>

</div>
