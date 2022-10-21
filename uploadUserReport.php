<?php
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';
session_start();

use Mpdf\Mpdf;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;


include('includes/functions.php');

$con = dbCon();

$userInfo = $_POST['userInfo'];
$appointment_id = $_POST['appointment_id'];
$type_id = $_POST['testType'];
$outputs = $_POST['mySource'];
$extensions = $_POST['extensions'];
$results = $_POST['results'];

// user info
date_default_timezone_set('US/Eastern');
$currenttime = date('Y-m-d h:i:s');
$type_id = $type_id;
$patient_id = $appointment_id;
$location = null;
$patient_firstname = $userInfo['firstName'];
$patient_lastname = $userInfo['lastName'];
$patient_phone = $userInfo['phone'];
$patient_email = $userInfo['email'];
$patient_birth = $userInfo['dob'];
$patient_gender = ($userInfo['gender'] == "Male")? 0: 1;
$patient_passport = $userInfo['passport_no'];
$sample_taken = $userInfo['updated_at'];
if ($type_id == 2)
    $patient_test_brand = "";
else if ($type_id == 5)
    $patient_test_brand = "OSOM ULTRA FLU A&B Screening";
else
    $patient_test_brand = NULL;
$user_id = $_SESSION['id'];
$released = isset($userInfo['updated_at']) ? $userInfo['updated_at'] : "";
$report_created_at = $currenttime;
$report_updated_at = $currenttime;
$handled_at = $currenttime;

$maxTokenQ = "SELECT user_token FROM `tbl_report` ORDER BY report_id DESC LIMIT 0,1";
$maxResult = mysqli_query($con, $maxTokenQ);
$token = mysqli_fetch_row($maxResult);
$userToken = (int)$token[0];
$newUserToken = str_pad($userToken + 1, 16, '0', STR_PAD_LEFT);

// var_dump($userInfo);die;

//Initial S3 Client
$client = new S3Client([
    'credentials' => [
        'key' => 'AKIAW7ZPM2DM4VGHHSFN',
        'secret' => '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'
    ],
    'region' => 'us-east-1',
    'version' => 'latest',
]);

// Generate the AWS S3 links
$awslinks = [];
$i = 0;
foreach($outputs as $output) :
    $awslink = "";

    switch($type_id) {
        case 1:
            $awslink = "63462f82e9pcr"  . $appointment_id . "_".$currenttime."_" . $i ."." . $extensions[$i];
        break;
        case 1:
            $awslink = "962f0aantigen"  . $appointment_id . "_".$currenttime."_" . $i ."." . $extensions[$i];
        break;
        case 1:
            $awslink = "0c9713eaccula"  . $appointment_id . "_".$currenttime."_" . $i ."." . $extensions[$i];
        break;
        case 1:
            $awslink = "2651eantibody"  . $appointment_id . "_".$currenttime."_" . $i ."." . $extensions[$i];
        break;
        case 1:
            $awslink = "67ecb2651eflu"  . $appointment_id . "_".$currenttime."_" . $i ."." . $extensions[$i];
        break;
        default:
        break;
    }
    array_push($awslinks, $awslink);
    $i ++;
endforeach;

$flag = false;
    
for($j = 0; $j < count($outputs); $j ++) :
    $sourceFile = $outputs[$j];
    $s3Link = $awslinks[$j];
    $testResult = $results[$j];
    // Seeing if the file exists on S3
    $response = $client->doesObjectExist('fasttestnowreports', 'REPORTS/' . $s3Link);

    
    if (!$response) {
        try {
            //    Delete File on S3
            $result = $client->deleteObject(array(
                'Bucket' => 'fasttestnowreports',
                'Key' => 'REPORTS/' . $s3Link,
            ));
        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    try {
        //    Upload File
        $result = $client->putObject([
            'Bucket' => 'fasttestnowreports',
            'Key' => 'REPORTS/' . $s3Link,
            'SourceFile' => $sourceFile
        ]);
        if ($result["@metadata"]["statusCode"] == '200') {
            $pdf_file_url = $result["ObjectURL"];
            $pdf_file_name = basename($result["ObjectURL"]);
            $sql = "INSERT INTO tbl_report (`report_id`, `user_token`, `patient_id`, `type_id`, `location`, `patient_firstname`, `patient_lastname`, `patient_phone`, `patient_email`, `patient_birth`, `patient_gender`, `patient_passport`, `report_results`, `sample_taken`,`user_id` , `released`, `report_created_at`, `handled_at`, `antigen_test_brand`, `pdf_file_url`, `pdf_file_name`) VALUES (NULL, '$newUserToken', $patient_id, '$type_id', '$location', '$patient_firstname', '$patient_lastname', '$patient_phone', '$patient_email', '$patient_birth', '$patient_gender', '$patient_passport', '$testResult', '$sample_taken', '$user_id', '$released', '$report_created_at', '$handled_at', '$patient_test_brand', '".$result["ObjectURL"]."', '" . $pdf_file_name . "')";
            // var_dump($sql);die;
            $q = mysqli_query($con, $sql);
            if($q) {
                if($j == count($outputs))
                    $flag = true;
            }
            unlink($sourceFile);
        }
    } catch (S3Exception $e) {
        echo ($e->getMessage());
    }

    
    
endfor;   

echo json_encode(array('result'=> $flag));
