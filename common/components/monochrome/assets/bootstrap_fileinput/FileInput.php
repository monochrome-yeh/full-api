<?php
namespace common\components\monochrome\assets\bootstrap_fileinput;

use yii\web\AssetBundle;


class FileInput extends AssetBundle {
	public $sourcePath = '@vendor/kartik-v/bootstrap-fileinput';

	public $css = [
		'css/fileinput.min.css',
	];
	public $js = [
	    'js/fileinput.min.js',
	];
	public $depends = [
	   'yii\web\YiiAsset', 
	   'yii\bootstrap\BootstrapAsset',
	];
} 		