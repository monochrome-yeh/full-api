<?php

use common\modules\monochrome\members\Members;

?>

<?= $form->field($user, 'reset_password')->radioList(['yes' => 'yes', 'no' => 'no'])->label(Members::t('app', 'Reset Password')); ?>
