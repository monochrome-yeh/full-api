<?php

use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\VendorUser;
?>

<?= $form->field($user, 'heir')->dropdownList(array_diff(VendorUser::getWorkerListByVendor($user->vid), [$user->getId() => $user->username]), ['prompt'=> Members::t('app', 'No Inheirt')]); ?>
<small class="text-warning"><?= Members::t('app', 'When active user, heir inherit data will be cancel.') ?></small>

<?= $form->field($user, 'activity', ['template' => '{input}'])->checkBox(['class' => 'js-switch no-icheck'])->label(Members::t('app', 'Do you want to enable this account?')); ?>