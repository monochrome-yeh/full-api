<?php

namespace common\components\monochrome\aws;

use Yii;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\S3\Exception\S3Exception;

class S3 extends \yii\base\Component
{

    public $key = '';

    public $secret = '';

    public $bucket = '';

    public $url;

    public $pre_folder = '';

    public $enable = true;

    public function uploadFile($fileObject, $path) {
        // Instantiate the client.
        if (!is_string($path) || $path == null) {
            return false;
        }    
        $credentials = new Credentials($this->key, $this->secret);
        $s3 = S3Client::factory(['credentials' => $credentials, 'region' => 'ap-northeast-1', 'version' => '2006-03-01']);
        $file = @fopen($fileObject->tempName, 'r+');
        $result = [];
        $result['status'] = false;
        try {
            // Upload data.
            $result = $s3->putObject(array(
                'Bucket' => $this->bucket,
                'Key'    => $this->pre_folder.$path,
                'Body'   => $file,
            ));
            $result['status'] = true;
        } catch (S3Exception $e) {
            $result['status'] = false;
            $result['messages'] = $e->getMessage();
        }
        @fclose($file);
        return $result;
    }

    public function createUrl($filePath) {
        if(is_string($filePath)) {
            return "{$this->url}/{$this->pre_folder}{$filePath}";
        }

        return null;
    }                        

}
