<?php

namespace common\components\monochrome\yahoo;

use yii\base\Widget;

class Weather extends Widget
{   
    public function run()
    {
        return $this->render('weather', [
        ]);
    }
}