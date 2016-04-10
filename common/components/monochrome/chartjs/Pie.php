<?php

namespace common\components\monochrome\chartjs;

use yii\base\Widget;

class Pie extends Widget
{
    public $data = [];

    public $options = [];

    public $clientOptions = [];

    public $legend = true;

    public $legendOptions = [];

    public function init()
    {
        parent::init();
    }
    
    private function getData()
    {
        $newData = [];
        $default = [
            'value' => 0,
            'color' =>'#46BFBD',
            'highlight' => '#5AD3D1',
            'label' => 'undefine',
            'labelColor'  => 'white',
            'labelFontSize'  => '16'
        ];

        foreach ($this->data as $data) {
            $newData[] = array_merge($default, $data);
        }

        return $newData;
    }

    private function getClientOptions()
    {
        $default = [
            // Boolean - Whether to show labels on the scale
            'scaleShowLabels' => true,

            // Interpolated JS string - can access value
            'scaleLabel' => '<%=value%>',

            // Boolean - Whether the scale should stick to integers, not floats even if drawing space is there
            'scaleIntegersOnly' => true,

            // Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            'scaleBeginAtZero' => false,

            // String - Scale label font declaration for the scale label
            'scaleFontFamily' => "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif'",

            // Number - Scale label font size in pixels
            'scaleFontSize' => 12,

            // String - Scale label font weight style
            'scaleFontStyle' => 'normal',

            // String - Scale label font colour
            'scaleFontColor' => '#666',

            // Boolean - Whether we should show a stroke on each segment
            'segmentShowStroke' => true,

            // String - The colour of each segment stroke
            'segmentStrokeColor' => '#fff',

            // Number - The width of each segment stroke
            'segmentStrokeWidth' => 2,

            // Number - The percentage of the chart that we cut out of the middle
            // percentageInnerCutout  => 50, // This is 0 for Pie charts

            // Number - Amount of animation steps
            'animationSteps' => 100,

            // String - Animation easing effect
            'animationEasing' => 'easeOutBounce',

            // Boolean - Whether we animate the rotation of the Doughnut
            'animateRotate' => true,

            // Boolean - Whether we animate scaling the Doughnut from the centre
            'animateScale' => false,

            // String - A legend template
            'legendTemplate' => '<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color :<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',

            // 'tooltipTemplate' => '<%if (label){%><%=label %> : <%}%><%= ((value/{$b})*100).toFixed(2) + \' %\' %>',

            'multiTooltipTemplate' => '<%= value + \' %\' %>',

            'responsive' => true
        ];

        return array_merge($default, $this->clientOptions);
    }

    public function run()
    {
        return $this->render('pie', [
            'data' => $this->getData(),
            'legend' => $this->legend,
            'clientOptions' => $this->getClientOptions(),
            'options' => $this->options
        ]);
    }
}
