<?= $form->field($user, 'account',[
    'enableLabel' => true, 
    'inputTemplate' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>',
    'inputOptions' => [
        'placeholder' => Yii::t('common/app', 'User Account'),
    ],
])->textInput(['readonly' => !Yii::$app->user->isVendorAdmin()]);
?>

<?= $form->field($user, 'email',[
    'enableLabel' => true, 
    'inputTemplate' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>',
    'inputOptions' => [
        'placeholder' => Yii::t('common/app', 'User Email'),
    ],
])->input('email');
?>

<?= $form->field($user, 'username',[
    'enableLabel' => true, 
    'inputTemplate' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>',
    'inputOptions' => [
        'placeholder' => Yii::t('common/app', 'User Name'),
    ],
]);
?>
<?php if (Yii::$app->user->getId() === (string)$user->_id) : ?>
<?= $form->field($fu, 'file')->fileInput(['accept' => 'image/*']) ?>
<?php endif ?>
