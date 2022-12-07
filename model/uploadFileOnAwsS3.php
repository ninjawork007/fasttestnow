<?php
error_reporting(E_ALL);
require_once('../vendor/autoload.php');

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;


class UploadFileOnAwsS3Client {
    private $localPath = "";

    function __construct($file) {
        $this->localPath = $file;
    }

    function initialize() {
        $client = new S3Client([
            'credentials' => [
                'key' => 'AKIAW7ZPM2DM4VGHHSFN',
                'secret' => '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'
            ],
            'region' => 'us-east-1',
            'version' => 'latest',
        ]);

        return $client;
    }

    function exists($file = NULL) {
        $client = $this->initialize();
        return $client->doesObjectExist('fasttestnowreports', 'REPORTS/' . $file);
    }

    function delete($file = NULL) {
        $client = $this->initialize();
        
        try {
            //    Delete File on S3
            $result = $client->deleteObject(array(
                'Bucket' => 'fasttestnowreports',
                'Key' => 'REPORTS/' . $file,
            ));
        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }
    function upload($awsFile = NULL) {

        //Initial S3 Client
        $client = $this->initialize();

        // Seeing if the file exists on S3
        if (!$this->exists($awsFile)) {
            $this->delete($awsFile);
        }

        try {
            //    Upload File
            $result = $client->putObject([
                'Bucket' => 'fasttestnowreports',
                'Key' => 'REPORTS/' . $awsFile,
                'SourceFile' => $this->localPath
            ]);
            if ($result["@metadata"]["statusCode"] == '200') {
                $pdf_file_url = $result["ObjectURL"];
                //    Delete PDF from Server Folder
                return $pdf_file_url;            
            }
        } catch (S3Exception $e) {
            return ($e->getMessage());
        }
    }
}






    


