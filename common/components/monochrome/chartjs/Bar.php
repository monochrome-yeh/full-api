<?php

namespace common\components\monochrome\chartjs;

use yii\base\Widget;

class Bar extends Widget
{
    public $unit = '%';
    public $labels = [];
    public $datasets = [];
    public $clientOptions = [];
    public $legend = true;

    public function init()
    {
        parent::init();
    }

    private function getData()
    {
        $data = [
            'labels' => $this->labels,
            'datasets' => [],
        ];

        $default = [
            'fillColor' => 'rgba(220, 220, 220, 0.75)',
            'highlightFill' => 'rgba(220, 220, 220, 1)',
        ];

        if (($datasets = $this->datasets) != null) {
            foreach ($datasets as $dataset) {
                $data['datasets'][] = array_merge($default, $dataset);
            }
        }

        return $data;
    }

    private function getClientOptions()
    {
        $default = [
            // Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            'scaleBeginAtZero' => true,
            // Boolean - Whether grid lines are shown across the chart
            'scaleShowGridLines' => true,
            // String - Colour of the grid lines
            'scaleGridLineColor' => 'rgba(0, 0, 0, .05)',
            // Number - Width of the grid lines
            'scaleGridLineWidth' => 1,
            // Boolean - Whether to show horizontal lines (except X axis)
            'scaleShowHorizontalLines' => true,
            // Boolean - Whether to show vertical lines (except Y axis)
            'scaleShowVerticalLines' => true,
            // Boolean - If there is a stroke on each bar
            'barShowStroke' => true,
            // Number - Pixel width of the bar stroke
            'barStrokeWidth' => 1,
            // Number - Spacing between each of the X value sets
            'barValueSpacing' => 5,
            // Number - Spacing between data sets within X values
            'barDatasetSpacing' => 10,
            //String - A legend template
            'legendTemplate' => '<ul class=\"<%=name.toLowerCase()%>-legend\"><% for(var i=0;i<datasets.length;i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
            'multiTooltipTemplate' => $this->unit != null ? '<%= datasetLabel %> <%= value + "' . $this->unit . '"' . ' %>' : '<%= datasetLabel %> <%= value %>',
        ];

        return array_merge($default, $this->clientOptions);
    }

    public function run()
    {
        return $this->render('bar', [
            'unit' => $this->unit,
            'data' => $this->getData(),
            'clientOptions' => $this->getClientOptions(),
            'legend' => $this->legend,
        ]);
    }
}
