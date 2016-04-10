<?php

namespace common\components\monochrome\fileupload\models;

use yii\base\Model;
use yii\web\UploadedFile;
use Yii;
/**
 * This is the model class for collection "project".
 *
 * @property \MongoId|string $_id
 * @property mixed $name
 */
class Fileupload extends Model
{

	public $file;
    private $_rules = [];
    private $_settings = [];
    private $_itemsPath = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return $this->_rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file' => Yii::t('common/app', 'File'),                      
        ];
    }  

    public function setSettings($params)
    {
        $params = array_merge([
            'checkExtensionByMimeType' => false,
            'skipOnEmpty' => false,
            'maxFiles' => 1,
            'encrypted' => true,
            'fileName' => '',
            'maxSize' => 1024 * 350,
        ], $params);

        $this->_settings = $params;

        if(isset($this->_settings['type'])) {            
            switch ($params['type']) {
                case 'csv':
                    $this->_rules = [
                        [['file'], 'file', 'checkExtensionByMimeType' => $this->_settings['checkExtensionByMimeType'], 'extensions' => 'csv', 'mimeTypes' => 'text/plain', 'maxSize' => $this->_settings['maxSize']],
                    ];
                    break;
                case 'image':
                    if ($this->_settings['maxFiles'] > 1) {
                        $this->_rules = [
                            [['file'], 'file', 'skipOnEmpty' => $this->_settings['skipOnEmpty'], 'extensions' => 'png, jpg', 'maxFiles' => $this->_settings['maxFiles'], 'maxSize' => $this->_settings['maxSize']],
                        ];
                    }
                    else {
                        $this->_rules = [
                            [['file'], 'file', 'skipOnEmpty' => $this->_settings['skipOnEmpty'], 'extensions' => 'png, jpg', 'maxSize' => $this->_settings['maxSize']],
                        ];                        
                    }
                    break;
            }            
        }
        else {
            die('set file type fail.');
        }
        
    }

    public function upload()
    {
        if ($this->validate() && !empty($this->file)) {
            $itemsPath = [];
            if (!is_array($this->file)) {
                $this->file = [$this->file];
            }
            foreach ((array)$this->file as $file) {
                if($this->_settings['encrypted'] || empty($this->_settings['fileName'])) {
                    $filename = uniqid($this->_settings['type'].'_', true);
                }
                else {
                    $filename = $this->_settings['fileName'];
                }

                $namePath = $this->_settings['path'] . $filename . '.' . $file->extension;

                if(Yii::$app->s3->enable) {
                    $result = Yii::$app->s3->uploadFile($file, $namePath);
                    if (!$result['status']) {
                        throw new \yii\web\ServerErrorHttpException( $result['messages'] );
                    }

                }
                else {
                    $file->saveAs('@webRoot/uploads/' . $namePath);      
                }

                $itemsPath[] = $namePath;
            }

            if ($this->_settings['maxFiles'] > 1) {
                $this->_itemsPath = $itemsPath;
            }
            else {
                $this->_itemsPath = $itemsPath[0];
            }       
            return true;
        } else {
            if (empty($this->getErrors()) && empty($this->file) && $this->_settings['skipOnEmpty']) {
                return true;
            }
        }

        return false;
    }

    public function getItemsPath()
    {
        return $this->_itemsPath;
    }   
}