<?php

use yii\helpers\Html;
use common\modules\monochrome\userReport\UserReport;

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\userReport\models\UserReport */

$this->title = UserReport::t('app', 'Create User Report');
// $this->params['breadcrumbs'][] = ['label' => UserReport::t('app', 'User Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-report-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
