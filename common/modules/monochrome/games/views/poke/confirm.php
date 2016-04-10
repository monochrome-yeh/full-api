<?php
use yii\helpers\Html;
use common\modules\monochrome\games\Games;

?>
<div class="page-header">
  <h1><?= Games::t('poke', 'Game Title') ?><small><?= Games::t('poke', 'Game Title SubText') ?></small></h1>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><?= Games::t('poke', 'Game Rules And Notice') ?></h3>
  </div>
  <div class="panel-body">
    <p>::遊戲規則，請先看過來:::  ．戳戳樂產生器Beta版，可以讓你自訂戳戳樂的結果選項！  </p>
    <p>戳戳樂產生器Beta版可以讓你發揮創意，但僅供趣味娛樂用，請勿濫用於賭博行為喔！</p>

<p>每一局戳戳樂遊戲的結果，由設定該局遊戲的人自行負責。</p>

<p>若您發現遊戲有bug或有任何建議／合作idea，歡迎與樂誌科技聯絡:)</p>

 
  </div>
</div>
<div class="row">
    <div class="text-center">
    	<?= Html::a(Games::t('poke', 'agree'), ['/games/poke/create'], ['class' => 'btn btn-success btn-lg', 'data-method'=>'post']) ?>
    	<?= Html::a(Games::t('poke', 'disagree'), ['/games/poke/index'], ['class' => 'btn btn-danger btn-lg']) ?>
    </div>
</div>