<?php

use logazine\Inspinia\ChartJsAsset;

ChartJsAsset::register($this);

$ctxId = 'ctx_' . uniqid();
$eleId = 'canv_' . uniqid();
$legendId = 'legendId_'. uniqid();

$data  = json_encode($data);
$clientOptions = json_encode($clientOptions);

$this->registerJs(
"
var {$ctxId} = document.getElementById('{$eleId}').getContext('2d');
var {$eleId} = new Chart({$ctxId}).Pie({$data}, {$clientOptions});
"
, $this::POS_END);

if ($legend) {

	$this->registerJs(
	"
		var {$legendId} = {$eleId}.generateLegend();
		$('#{$legendId}').html({$legendId});
		"
	, $this::POS_END);

	$this->registerCss(
	"
	.pie-legend li span {
	    width: 1em;
	    height: 1em;
	    display: inline-block;
	    margin-right: 5px;
	}

	.pie-legend {
	    list-style: none;
	}
	"
	);
}	

?>

<canvas id="<?= $eleId ?>"></canvas>

<?php if ($legend) : ?>
<div id="<?= $legendId ?>"></div>
<?php endif ?>
