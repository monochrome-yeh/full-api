<?php

namespace common\components\monochrome\chartjs;

use yii\base\Widget;

class Line extends Widget
{
    private $_datasets_count = 0;

    public $unit;
    public $label;
    public $lines = [];
    public $select = [];
    public $datasets = [];
    public $clientOptions = [];
    public $legend = true;
    public $multiple = true;
    public $isTenThousand = false;

    public function init()
    {
        parent::init();

        $this->_datasets_count = count($this->datasets);
    }

    private function getLine()
    {
        $line = [
            'labels' => $this->label,
            'datasets' => [],
        ];
        $default = [
            'pointStrokeColor' => '#fff',
            'pointHighlightFill' => '#fff',
            'pointHighlightStroke' => 'rgba(220, 220, 220, 1)',
        ];

        if ($this->multiple) {
            foreach ($this->lines as $type => $options) {
                $line['datasets'][] = ['data' => $this->datasets['items'][$type]] + $options + $default;
            }
        } else {
            $line['datasets'][] = ['data' => $this->datasets] + $this->lines + $default;
        }

        return $line;
    }

    private function getSelect()
    {
        return $this->select + [
            'id' => 'selectId_'.uniqid(),
            'name' => 'selectName_'.uniqid(),
            'options' => [],
        ];
    }

    private function getClientOptions()
    {
        $result = $this->clientOptions + [
            'responsive' => false,
            'bezierCurve' => true,
            'bezierCurveTension' => 0.4,
            'datasetFill' => true,
            'datasetStroke' => true,
            'datasetStrokeWidth' => 2,
            'pointDot' => true,
            'pointDotRadius' => 4,
            'pointDotStrokeWidth' => 1,
            'pointHitDetectionRadius' => 20,
            'scaleSteps' => 10,
            'scaleStepWidth' => 1,
            'scaleStartValue' => 0,
            'scaleOverride' => true,
            'scaleShowGridLines' => true,
            'scaleGridLineWidth' => 1,
            'scaleGridLineColor' => 'rgba(0, 0, 0, .05)',
            'scaleLabel' => '<%= value %>',
            'tooltipTemplate' => '<%= label %> <%= value %>',
            'multiTooltipTemplate' => '<%= datasetLabel %> <%= value %>',
            'legendTemplate' => '<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>'
        ];

        if ($this->isTenThousand && (($this->_datasets_count == 1 && !array_key_exists('none', $this->datasets)) || $this->_datasets_count > 1)) {
            $result['scaleLabel'] = str_replace('value', '(value/10000).toString() + "萬"', $result['scaleLabel']);
            $result['tooltipTemplate'] = str_replace('value', '(value/10000).toString() + "萬"', $result['tooltipTemplate']);
            $result['multiTooltipTemplate'] = str_replace('value', '(value/10000).toString() + "萬"', $result['multiTooltipTemplate']);
        }

        return $result;
    }

    public function run()
    {
        return $this->render('line', [
            'unit' => $this->unit,
            'legend' => $this->legend,
            'line' => $this->getLine(),
            'select' => $this->getSelect(),
            'clientOptions' => $this->getClientOptions(),
        ]);
    }
}
