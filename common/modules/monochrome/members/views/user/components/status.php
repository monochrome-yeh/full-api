<?php

use common\modules\monochrome\members\Members;

?>

<?= $form->field($user, 'activity')->checkBox()->label(Members::t('app', 'Do you want to enable this account?')); ?>
