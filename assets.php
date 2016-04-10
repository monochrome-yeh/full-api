<?php
/**
 * Configuration file for the "yii asset" console command.
 */

// In the console environment, some path aliases may not exist. Please define these:
// Yii::setAlias('@webroot', __DIR__ . '/../web');
Yii::setAlias('web', '/');
Yii::setAlias('webroot', __DIR__ . '/frontend/web');
Yii::setAlias('@logazine', __DIR__ . '/vendor/logazine');
return [
    // Adjust command/callback for JavaScript files compressing:
    'jsCompressor' => 'java -jar /usr/share/java/closure-compiler.jar --js {from} --js_output_file {to}',
    // Adjust command/callback for CSS files compressing:
    'cssCompressor' => 'java -jar /usr/share/yui-compressor/yui-compressor.jar --type css {from} -o {to}',
    // The list of asset bundles to compress:
    'bundles' => [
	'logazine\Inspinia\InspiniaAppAsset',
        // 'app\assets\AppAsset',
        // 'yii\web\YiiAsset',
        // 'yii\web\JqueryAsset',
    ],
    // Asset bundle for compression output:
    'targets' => [
        'all' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot',
            'baseUrl' => '@web',
            'js' => 'js/all.js',
            'css' => 'css/all.css',
            // 'cssOptions' => [
            //     'media' => 'all',
            // ]     
        ],    
    ],
    // Asset manager configuration:
    'assetManager' => [
    	'baseUrl' => '@web',
    	'basePath' => '@webroot/assets',
        'linkAssets' => true,      
    ],
];
