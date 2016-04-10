<?php
use common\modules\monochrome\members\models\user_settings\Settings;
?>

<?= $form->field($user, 'settings[font_size]')->dropdownList(Settings::getSizeList())->label(Yii::t('common/app', 'Font Size')); ?>
