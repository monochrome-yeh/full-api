<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\monochrome\request\Request;

$this->registerJs(
"
$('.to-do-check').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'iradio_square-blue'
});

$('.to-do-check').on('ifClicked', function(event) {
    var checkId = this.id;
    var thisItem = $(this);

    if (confirm('".Request::t('app', 'Are you sure you have done this to do list?')."')) {
        $.ajax({
            type: 'POST',
            url: '".Url::toRoute(['/request/to-do-list/done'])."' + '/' + checkId,
        });
    } else {
        thisItem.iCheck('check');
        setTimeout(function() {
            thisItem.iCheck('uncheck');
        }, 100);
    }
});
"
, View::POS_READY, 'to-do-list-js');

?>

<div class="ibox-title"><strong><?= Request::t('app', 'New To Do List') ?></strong></div>
<div class="ibox ibox-content">
    <div class="chat-activity-list">
        <?php if($items != null): ?>
            <?php foreach($items as $item): ?>
                <div class="chat-element">
                    <div class="pull-left">
                        <input type="checkbox" id="<?= $item['id'] ?>" class="i-checks to-do-check no-icheck" />
                    </div>
                    <div class="media-body row">
                        <div class="col-md-2 col-xs-12 col-sm-2">
                            <strong class="text-success"><?= $item['assigner'] ?></strong>
                        </div>
                        <div class="col-sm-6  col-md-8 col-xs-6">
                            <p class="m-b-xs"><?= Html::encode($item['content']) ?></p>
                        </div>
                        <div class="col-sm-4 col-md-2 col-xs-6">
                            <small class="pull-right text-navy"><?= Html::encode($item['created_at']) ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <?= Request::t('app', 'There is no to do list.') ?>
        <?php endif ?>
    </div>
</div>
