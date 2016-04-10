<?php

use Yii;

?>

<?= $form->field($user, 'account',[
    'enableLabel' => false, 
    'inputTemplate' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>',
    'inputOptions' => [
        'placeholder' => Yii::t('common/app', 'User Account'),
    ],
])->textInput(['readonly' => !$user->isNewRecord]);
?>

<?= $form->field($user, 'username',[
    'enableLabel' => false, 
    'inputTemplate' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>',
    'inputOptions' => [
        'placeholder' => Yii::t('common/app', 'User Name'),
    ],
]);
?>
