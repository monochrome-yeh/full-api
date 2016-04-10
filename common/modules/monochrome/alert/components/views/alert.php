<?php

use yii\bootstrap\Nav;

echo Nav::widget([
    'encodeLabels' => false,
    'dropDownCaret' => '',
    'options' => ['class' => 'nav navbar-top-links navbar-right'],
    'items' => $items,
]);

?>
