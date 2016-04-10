<?php

use yii\jui\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\monochrome\request\Request

/* @var $this yii\web\View */
/* @var $model common\modules\monochrome\request\models\ToDoList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="assign-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
        // echo $form->field($model, 'deadline')->widget(DatePicker::classname(), [
        //     'options' => ['class' => 'form-control', 'readonly' => true],
        //     'clientOptions' => [
        //         'changeYear' => true,
        //         'changeMonth' => true,
        //         'minDate' => 0,
        //     ],
        //     //'language' => 'ru',
        //     //'dateFormat' => 'yyyy-MM-dd',
        // ])
    ?>

    <?php if ($model->isNewRecord): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <?= $form->field($model, 'roles', ['template' => '{label}', 'options' => ['css' => '']])->checkBoxList($model->getRolesOptions()) ?>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'roles')->checkBoxList($model->getRolesOptions())->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <?= $form->field($model, 'pids', ['template' => '{label}',  'options' => ['css' => '']])->checkBoxList($projectList) ?>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'pids')->checkBoxList($projectList)->label(false); ?>
                    </div>
                </div>
            </div>
        </div>

        <?= $form->field($model, 'content')->textArea() ?>
    <?php else: ?>
        <?php // echo $this->render('_detail_info', ['model' => $model, 'showDeadline' => false]) ?>
        <?= $this->render('_detail_info', ['model' => $model]) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common/app', 'Create') : Yii::t('common/app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
