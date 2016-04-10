<?php

use yii\helpers\Html;
use logazine\Inspinia\ChartJsAsset;

ChartJsAsset::register($this);

$ctxId = 'ctx_'.uniqid();
$legendId = 'legend_'.uniqid();
$elementId = 'canvas_'.uniqid();
$jsonAllLine = json_encode($line);
$jsonAllClientOptions = json_encode($clientOptions);

$this->registerCss("
    #{$elementId} {
        width: 100% !important;
        height: 250px !important;
    }
");

$this->registerJs("
    var {$elementId} = {$elementId} || {};
    var {$ctxId} = document.getElementById('{$elementId}').getContext('2d');
    var getLine = function() { return {$jsonAllLine}; };
    var getClientOptions = function() { return {$jsonAllClientOptions}; };
    var line = getLine();
    var clientOptions = getClientOptions();
", $this::POS_END);    

if (isset($select)) {
    $selectId = $select['id'];
    $selectName = $select['name'];
    $selectOptions = $select['options'];
    $jsonAllSelectOptions = json_encode($selectOptions);

    if ($selectOptions != null) {
        echo '<span class="pull-right">'.Html::dropdownList($selectName, Yii::$app->formatter->asDate(time(), 'php:Y'), $selectOptions, ['id' => $selectId]).'</span>';
    }

    $this->registerJs("
        var value = $('#{$selectId}').val();
        var options = {$jsonAllSelectOptions};

        if (!options.hasOwnProperty(value)) {
            value = 'none';
        }

        function setChartLineData(v) {
            line = getLine();
            clientOptions = getClientOptions();
            $.each(line['datasets'], function(index, result) {
                line['datasets'][index]['data'] = result['data'][v];
            });

            $.each(clientOptions, function(index, result) {
                if ($.isPlainObject(result)) {
                    clientOptions[index] = result[v];
                }
            });
        }

        $('#{$selectId}').on('change', function(e) {
            setChartLineData($(this).val());
            {$elementId}.destroy();
            {$elementId} = new Chart({$ctxId}).Line(line, clientOptions);
        });

        setChartLineData(value);
    ", $this::POS_END);
}

$this->registerJs("
    {$elementId} = new Chart({$ctxId}).Line(line, clientOptions);

    $(window).on('resize orientationchange', function(e) {
        {$elementId}.destroy();
        {$elementId} = new Chart({$ctxId}).Line(line, clientOptions);
    });
", $this::POS_END);

if ($legend) {
    $this->registerJs("
        var {$legendId} = {$elementId}.generateLegend();
        $('#{$legendId}').html({$legendId});
    ", $this::POS_END);

    $this->registerCss("
        .line-legend {
            list-style: none;
        }

        .line-legend li {
            display: inline-block;
            margin-right: 5px;
        }

        .line-legend li span {
            width: 1em;
            height: 1em;
            display: inline-block;
            margin-right: 5px;
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
