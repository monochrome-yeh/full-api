<?php

$countyName = \yii\helpers\StringHelper::basename(get_class($model)) . "[{$countyAttributeName}]";
$countyId = strtolower(\yii\helpers\StringHelper::basename(get_class($model))) . "-{$countyAttributeName}";
$districtName = \yii\helpers\StringHelper::basename(get_class($model)) . "[{$districtAttributeName}]";
$districtId = strtolower(\yii\helpers\StringHelper::basename(get_class($model))) . "-{$districtAttributeName}";
$uniqueClassName = "mark_twzipcode_{$countyAttributeName}";


$this->registerJsFile(Yii::getAlias('@web').'/js/jquery.twzipcode.min.js', ['depends' => [\yii\web\JqueryAsset::className()], 'position' => $this::POS_END]); 
$this->registerJs(
    "
    $('.{$uniqueClassName}').twzipcode({
        readonly : true,
        zipcodeIntoDistrict : true,
        countyName : '{$countyName}',
        districtName : '{$districtName}',
        countySel : '{$model->$countyAttributeName}',
        districtSel : '{$model->$districtAttributeName}',
    });
    $('.{$uniqueClassName}').find('select[name=\"{$countyName}\"]').attr('id', '{$countyId}');
    $('.{$uniqueClassName}').find('select[name=\"{$districtName}\"]').attr('id', '{$districtId}');
    "
);

?>

<style type="text/css">
.style {
    margin-bottom: 10px;
}
.form-group {
    margin-bottom: 0px;
}
</style>

<div class="<?= $uniqueClassName ?>">
    <?php
        $countyInput = "{label}\n<div data-role='county' data-style='form-control style'></div>\n{error}\n{hint}";
        $districtInput = "{label}\n<div data-role='district' data-style='form-control style'></div>\n{error}\n{hint}";
        if ($enableLabel === false) {
            $countyInput = "<div data-role='county' data-style='form-control style'></div>\n{error}\n{hint}";
            $districtInput = "<div data-role='district' data-style='form-control style'></div>\n{error}\n{hint}";
        }
    ?>
    <?= $form->field($model, $countyAttributeName, ['template' => $countyInput]); ?>
    <?= $form->field($model, $districtAttributeName, ['template' => $districtInput]); ?>
</div>
