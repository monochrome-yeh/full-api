<?php

use logazine\Inspinia\ChartJsAsset;

ChartJsAsset::register($this);

$ctxId = 'ctx_'.uniqid();
$legendId = 'legend_'.uniqid();
$elementId = 'canvas_'.uniqid();

$data  = json_encode($data);
$clientOptions = json_encode($clientOptions);

$this->registerCss("
    #{$elementId} {
        width: 100% !important;
        height: 300px !important;
    }
");

$this->registerJs("
	var {$ctxId} = document.getElementById('{$elementId}').getContext('2d');
	var {$elementId} = new Chart({$ctxId}).Bar({$data}, {$clientOptions});

    $(window).on('resize orientationchange', function(e) {
        {$elementId}.destroy();
        {$elementId} = new Chart({$ctxId}).Bar({$data}, {$clientOptions});
    });
", $this::POS_END);

if ($legend) {
	$this->registerJs("
		var {$legendId} = {$elementId}.generateLegend();
		$('#{$legendId}').html({$legendId});
	", $this::POS_END);

	$this->registerCss("
		.bar-legend li span {
		    width: 1em;
		    height: 1em;
		    display: inline-block;
		    margin-right: 5px;
		}

		.bar-legend {
		    list-style: none;
		}
	");
}	

?>

<?php if ($unit != null): ?>
    <span class="pull-left">單位：<?= $unit ?></span>
<?php endif ?>
<canvas id="<?= $elementId ?>"></canvas>
<?php if ($legend): ?>
	<div id="<?= $legendId ?>"></div>
<?php endif ?>
