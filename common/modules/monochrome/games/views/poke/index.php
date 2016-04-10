<?php
use yii\helpers\Html;
use common\modules\monochrome\games\Games;
?>
<div class="content">
    <div class="jumbotron">
        <h1><?= Games::t('poke', 'Game Title') ?></h1>

        <p class="lead"><?= Games::t('poke', 'Game Title SubText') ?></p>

        <p><?= Html::a(Games::t('poke', 'Create Game'), ['/games/poke/create'], ['id' => 'createGame', 'class' => 'btn btn-success btn-lg']) ?></p>
    </div>	
<?= Html::a('Privacy', ['/members/default/auth','authclient' => 'facebook'], ['id' => 'createGame', 'class' => 'btn btn-danger btn-lg']) ?>
</div>