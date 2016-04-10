<?php

use common\modules\monochrome\members\Members;

?>

<?= $form->field($user, 'password', [
    'inputOptions' => ['placeholder' => Members::t('app', 'Password'), 'value' => '',]
])->label(Members::t('app', 'Password'))->passwordInput(); ?>

<?= $form->field($user, 'password_repeat', [
    'inputOptions' => ['placeholder' => Members::t('app', 'Password Confirmation'), 'value' => '',]
])->label(Members::t('app', 'Password Confirmation'))->passwordInput(); ?>
