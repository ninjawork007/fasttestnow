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
                if(($j+1) == count($outputs))
                    $flag = true;
            }
            unlink($sourceFile);
            // Send OTP
			require_once('../postmark.php');
			// include "postmark.php";
			$receiver = $this->email;
			$subject = "Fast Test Now Upload Results";
			$message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
                        <html>
                        
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                            <title>Fast test now</title>
                            <style type="text/css">
                                html {
                                    -webkit-text-size-adjust: none;
                                    -ms-text-size-adjust: none;
                                }
                        
                                @media only screen and (max-device-width: 680px),
                                only screen and (max-width: 680px) {
                                    *[class="table_width_100"] {
                                        width: 96% !important;
                                    }
                        
                                    *[class="border-right_mob"] {
                                        border-right: 1px solid #dddddd;
                                    }
                        
                                    *[class="mob_100"] {
                                        width: 100% !important;
                                    }
                        
                                    *[class="mob_center"] {
                                        text-align: center !important;
                                    }
                        
                                    *[class="mob_center_bl"] {
                                        float: none !important;
                                        display: block !important;
                                        margin: 0px auto;
                                    }
                        
                                    .iage_footer a {
                                        text-decoration: none;
                                        color: #929ca8;
                                    }
                        
                                    img.mob_display_none {
                                        width: 0px !important;
                                        height: 0px !important;
                                        display: none !important;
                                    }
                        
                                    img.mob_width_50 {
                                        width: 40% !important;
                                        height: auto !important;
                                    }
                                }
                        
                                .table_width_100 {
                                    width: 680px;
                                }
                            </style>
                        </head>
                        
                        <body style="padding: 0px; margin: 0px;">
                            <div id="mailsub" class="notification" align="center">
                                <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#596167">
                                    <span
                                        style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; color: #2a2929;">
                                        You\'ve received an encrypted message from FastTestNow&#174;
                                    </span>
                                </font>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 320px;">
                                    <tr>
                                        <td align="center" bgcolor="#eff3f8">
                        
                        
                                            <!--[if gte mso 10]>
                                <table width="680" border="0" cellspacing="0" cellpadding="0">
                                <tr><td>
                                <![endif]-->
                        
                                            <table border="0" cellspacing="0" cellpadding="0" class="table_width_100" width="100%"
                                                style="max-width: 680px; min-width: 300px;">
                                                <tr>
                                                    <td>
                                                        <!-- padding -->
                                                        <div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
                                                    </td>
                                                </tr>
                                                <!--header -->
                                                <tr>
                                                    <td align="center" bgcolor="#ffffff">
                                                        <!-- padding -->
                                                        <div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
                                                        <table width="90%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td align="center">
                                                                    <a href="https://fasttestnow.com/" target="_blank"
                                                                        style="color: #596167; font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
                                                                        <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3"
                                                                            color="#596167">
                                                                            <img src="https://fasttestnow.health/images/fast.png" width="135"
                                                                                alt="FastTestNow" border="0" style="display: block;" />
                                                                        </font>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <!-- padding -->
                                                        <div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
                                                    </td>
                                                </tr>
                                                <!--header END-->
                        
                                                <!--goods -->
                                                <tr>
                                                    <td align="center" bgcolor="#ffffff">
                                                        <table width="90%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td align="center">
                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                                                        style="line-height: 14px; padding: 0 25px;">
                                                                        <tbody>
                                                                            <tr>
                                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height: 14px; padding: 0 25px;">
                                                                                    <tbody><tr><td style="width: 14%;">
                                                                                        <font face="Arial, Helvetica, sans-serif" size="5" color="#57697e" style="font-size: 22px;">
                                                                                        <span style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #2a2929; font-weight: bold;">
                                                                                            Results:
                                                                                        </span></font>
                                                                                        <!-- padding --><div style="height: 60px; line-height: 60px; font-size: 10px;">&nbsp;</div>
                                                                                    </td><td>
                                                                                        <a href="' . $pdf_file_url . '" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold;">
                                                                                            <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#0074fe">
                                                                                            Download Submission PDF	</font></a>
                                                                                        <!-- padding --><div style="height: 60px; line-height: 60px; font-size: 10px;">&nbsp;</div>
                                                                                    </td></tr>			
                                                                                    </tbody>
                                                                                </table>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                        
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <!-- padding -->
                                                                    <div style="height: 10px; line-height: 10px; font-size: 10px;">&nbsp;</div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!--goods END-->
                        
                                                <!--footer -->
                                                <tr>
                                                    <td class="iage_footer" align="center" bgcolor="#fcfafb"
                                                        style="border-top-width: 1px; border-top-style: solid; border-top-color: #ffffff;">
                                                        <!-- padding -->
                                                        <div style="height: 30px; line-height: 30px; font-size: 10px;">&nbsp;</div>
                        
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td align="center">
                                                                    <font face="Arial, Helvetica, sans-serif" size="3" color="#717171"
                                                                        style="font-size: 13px;">
                                                                        <span
                                                                            style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #717171;">
                                                                            ' . date('Y') . ' &copy; FastTestNow. ALL Rights Reserved.
                                                                        </span>
                                                                    </font>
                                                                </td>
                                                            </tr>
                                                        </table>
                        
                                                        <!-- padding -->
                                                        <div style="height: 30px; line-height: 30px; font-size: 10px;">&nbsp;</div>
                                                    </td>
                                                </tr>
                                                <!--footer END-->
                                                <tr>
                                                    <td>
                                                        <!-- padding -->
                                                        <div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!--[if gte mso 10]>
                                </td></tr>
                                </table>
                                <![endif]-->
                        
                                        </td>
                                    </tr>
                                </table>
                        
                            </div>
                        </body>
                        
                        </html>';
        }
    } catch (S3Exception $e) {
        echo ($e->getMessage());
    }

    
    
endfor;   

echo json_encode(array('result'=> $flag));
