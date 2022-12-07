<?php
use Mpdf\Mpdf;

include('../includes/functions.php');
include('../model/uploadFileOnAwsS3.php');

class Strep {

    private $data;

    function __construct($entities) {
        $this->data = $entities;
    }
    function create() {
        $conn = dbCon();
		$sql = "CREATE TABLE tbl_strep (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            appointment_id INT(11) NOT NULL,
            user_token VARCHAR(30) NOT NULL DEFAULT 0,
            firstname VARCHAR(30) NOT NULL,
            lastname VARCHAR(30) NOT NULL,
            email VARCHAR(50),
            gender INT(2) NOT NULL,
            results INT(2) NOT NULL,
            dob VARCHAR(30) NOT NULL,
            pdfLink VARCHAR(255) NOT NULL,
            handled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
         
		$result = mysqli_query($conn,$sql);
        
		if ($result === TRUE) {
			return (boolean) true;
        } else {
            return (boolean) false;
        }
    }
    function isTable() {
        $conn = dbCon();
        $val = mysqli_query($conn,"SELECT * FROM tbl_strep LIMIT 1");
        if($val !== FALSE) {
			return (boolean) true;
        } else {
            return $this->create();
        }
    }

    function insert($keys) {
        $conn = dbCon(); 
        if($this->isTable() === true) {
            $importData = implode("', '", $this->data);
            $params = implode("`, `", $keys);
            $sql = "INSERT INTO tbl_strep (`".$params."`) VALUES ('".$importData."')";
            
            $q = mysqli_query($conn, $sql);
            $id = mysqli_insert_id($conn);
            if ($q === true) {
                $sql = "UPDATE tbl_strep SET `user_token`='".$this->userToken($id)."' WHERE id=". $id;
                $t = mysqli_query($conn, $sql);
                if ($t === true) {
                    return array('id' => $id, 'result' => $q);
                }
            } else {
                return array('id' => 0, 'result' => (boolean) false);
            }
        } else {
            var_dump("Failed in Creating tbl_strep Table"); die;
        }
    }

    function userToken($id) {
        $conn = dbCon(); 
        $maxTokenQ = "SELECT user_token FROM `tbl_strep` WHERE id NOT IN ($id) ORDER BY id DESC LIMIT 0,1";
        $maxResult = mysqli_query($conn, $maxTokenQ);
        $token = mysqli_fetch_row($maxResult);
        if($token == NULL)
            $userToken = 0;
        else 
            $userToken = (int)$token[0];
        $newUserToken = str_pad($userToken + 1, 16, '0', STR_PAD_LEFT);

        return $newUserToken;
    }

    function update($id = NULL) {
        $conn = dbCon(); 
        
        $sql = "UPDATE tbl_strep SET ".$this->data." WHERE id=". $id;
        
        $q = mysqli_query($conn, $sql);
        if ($q === true) 
            return array('id' => $id, 'result' => $q);
        else 
            return array('id' => 0, 'result' => (boolean) false);
    }

    function delete($id = NULL) {
        $conn = dbCon();

        $sql = "DELETE FROM tbl_strep WHERE id=".$id;
        $q = mysqli_query($conn, $sql);

        if ($q === true) 
            return array('id' => $id, 'result' => $q);
        else 
            return array('id' => 0, 'result' => (boolean) false);
    }

    function findById($id = NULL) {
        $conn = dbCon();

        $sql = "SELECT * FROM  tbl_strep WHERE id=".$id;
        $q = mysqli_query($conn, $sql);
        $data = mysqli_fetch_assoc($q);

        return $data;
    }

    function findAll() {
        $conn = dbCon();

        $sql = "SELECT * FROM  tbl_strep";
        $q = mysqli_query($conn, $sql);
        $data = mysqli_fetch_assoc($q);

        return $data;
    }

    // pdf manipulation
    function generatePDF($id) {

        $data = $this->data;
        $output = "../uploads/c742a402367f2030636af25ce0c9713estrep" . $id . ".pdf";
        require_once ('../vendor/autoload.php');

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 10,
            'format' => 'A4',
            'default_font' => 'Roboto-Regular'
        ]);

        $result_img = ($data['results'] == 0) ? "../assets/images/negative.png" : "../assets/images/positive.png";

        $pdfcontent = '';
        $pdfcontent .= '<style>
            .results { width: 120px; }
            .barcode { padding: 1.5mm; margin: 0; vertical-align: top; color: #000000;}
            .barcodecell { text-align: center; vertical-align: middle; padding: 0;}
            .logo { width: 170px; margin-left: 40px;}
            .block { margin-right: 40px; margin-left: 40px;}
            .header { height:350px; width: 100%; vertical-align: bottom; background-color:#dad7e3; }
            .main_text { font-size: 18px;}
            .second_text { /* color: #57697e; */ } </style><body>';
        $mpdf->SetFont("Roboto-Regular");
        $pdfcontent .= '<table width="100%" class="header">
                        <tr><td >&nbsp;</td></tr>
                        <tr><td >&nbsp;</td></tr>
                        <tr>
                        <td width="28%;"><input type="image" class="logo" src="../images/fast.png" /></td>
                        
                        <td width="40%;" style="text-align: center;vertical-align:middle;">
                            <h2>Strep Screening</h2>
                        </td>
                        <td style="text-align: left;vertical-align:middle;">
                            <span style="font-size:12px;">Patient ID: &nbsp;&nbsp;&nbsp;&nbsp;' . $data['user_token'] . '</span> <br/><br/>
                            <h3>' . date("D, M d, Y") . '</h3>
                        </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                    </table>';

        $pdfcontent .= '<table border="0" class="block main_text" style="width: 100%;" >';
        $pdfcontent .= '<tr><td>&nbsp;</td></tr>';
        $pdfcontent .= '<tr>';
        $pdfcontent .= '<td style="width: 50%;"  colspan="2"><b>Date & Time Order Confirmation</b></td>';
        $pdfcontent .= '<td style="width: 50%;"  colspan="2"><b>Date of Birth</b></td>';
        $pdfcontent .= '</tr>';
        $pdfcontent .= '<tr>';
        $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
        $pdfcontent .= $data['handled_at'];
        $pdfcontent .= '</td>';
        $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
        $pdfcontent .= $data['dob'];
        $pdfcontent .= '</td>';
        $pdfcontent .= '</tr>';
        $pdfcontent .= '<tr>';
        $pdfcontent .= '<td style="width: 50%;"  colspan="2"><b>Patient Name</b></td>';
        $pdfcontent .= '<td style="width: 50%;"  colspan="2"><b>Email</b></td>';
        $pdfcontent .= '</tr>';
        $pdfcontent .= '<tr>';
        $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $data['firstname'] . ' ' . $data['lastname'] . '</td>';
        $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $data['email'] . '</td>';
        $pdfcontent .= '</tr>';
        $pdfcontent .= '<tr >';
        $pdfcontent .= '<td style="width: 15%;height: 50px">';
        $pdfcontent .= '<b>Gender</b>';
        $pdfcontent .= '</td>';
        $pdfcontent .= '<td style="text-align:left;color:#6a7180">';
        $pdfcontent .= ($data['gender'] == 0) ? "Male" : "Female";;
        $pdfcontent .= '</td>';
        $pdfcontent .= '</tr>';
        $pdfcontent .= '<tr>';
        $pdfcontent .= '<td style="width: 15%; height: 50px" >';
        $pdfcontent .= '<b>Results</b>';
        $pdfcontent .= '</td>';
        $pdfcontent .= '<td style="width: 40%;text-align:center" >';
        $pdfcontent .= '<input type="image" class="results" src="' . $result_img . '" />';
        $pdfcontent .= '</td>';
        $pdfcontent .= '</tr>';
        $pdfcontent .= '</table>';

        $pdfcontent .= '<table  class="block" border="0" style="width: 100%;font-size: 12px;border-top: 0.1px solid #333333;margin-top: 60%; bottom: 50px;">';
        $pdfcontent .= '<tr>';
        $pdfcontent .= '<td rowspan="3"  class="barcodecell">';
        $pdfcontent .= '<barcode code="https://fasttestnow.com" type="QR" class="barcode" size="1" error="M" />';
        $pdfcontent .= '</td>';
        $pdfcontent .= '<td style="text-align: right;">';
        $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
        $pdfcontent .= '</td>';
        $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
        $pdfcontent .= '<img src="../images/sign.jpg" style="width: 90px;"/>';
        $pdfcontent .= '</td>';
        $pdfcontent .= '</tr>';
        $pdfcontent .= '<tr>';
        $pdfcontent .= '<td style="text-align: right;">';
        $pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
        // $pdfcontent .= $test_location;
        $pdfcontent .= '</td>';
        $pdfcontent .= '</tr>';
        $pdfcontent .= '<tr>';
        $pdfcontent .= '<td style="text-align: right;">';
        $pdfcontent .= 'Physician Oversight: Dr. Dominique M Thompson #ME134892';
        $pdfcontent .= '</td>';
        $pdfcontent .= '</tr>';
        $pdfcontent .= '</table>';
        $pdfcontent .= '</body>';

        $mpdf->WriteHTML($pdfcontent);

        //output in browser
        $mpdf->Output($output, 'F');

        // upload the pdf on AWS S3
        $awsPdfFile = "8513da060dea559ed3dff467ecb2651estrep" . $id . ".pdf";

        $url = $this->uploadReportToAwsS3($output, $awsPdfFile);
        
        $conn = dbCon();
        $sql = "UPDATE tbl_strep SET pdfLink='".$url."' WHERE id=". $id;
        $q = mysqli_query($conn, $sql);
        
        if($q) {
            return $this->sendEmail($this->data, $output);
        }
    }

    function uploadReportToAwsS3($localPath, $awsPath) {
        $sourceFile = $localPath;
        $s3Link = $awsPath;
        

        $awsS3 = new UploadFileOnAwsS3Client($sourceFile);
        $result = $awsS3->upload($s3Link);
        return $result;
    }

    function sendEmail($data, $URL) {
        include "../libraries/postmark.php";

        $receiver = $data['email'];
        $pdf_name = "Strep Screening";
        $subject = "Strep Screening [" . $data['firstname'] . " " . $data['lastname'] . "]";
        $message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
        <title>Fast test now</title>
        <style type="text/css">
        html { -webkit-text-size-adjust:none; -ms-text-size-adjust: none;}
        @media only screen and (max-device-width: 680px), only screen and (max-width: 680px) { 
            *[class="table_width_100"] { width: 96% !important; }
            *[class="border-right_mob"] { border-right: 1px solid #dddddd; }
            *[class="mob_100"] { width: 100% !important; }
            *[class="mob_center"] { text-align: center !important; }
            *[class="mob_center_bl"] { float: none !important; display: block !important; margin: 0px auto; }	
            .iage_footer a { text-decoration: none; color: #929ca8; }
            img.mob_display_none { width: 0px !important; height: 0px !important; display: none !important; }
            img.mob_width_50 { width: 40% !important; height: auto !important; }
        }
        .table_width_100 { width: 680px; }
        </style>
        </head>
        
        <body style="padding: 0px; margin: 0px;">
        <div id="mailsub" class="notification" align="center">
        <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#596167">
            <span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; color: #2a2929;">
                                You\'ve received an encrypted message from FastTestNow&#174; 
                            </span></font>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 320px;"><tr><td align="center" bgcolor="#eff3f8">
        
        
        <!--[if gte mso 10]>
        <table width="680" border="0" cellspacing="0" cellpadding="0">
        <tr><td>
        <![endif]-->
        
        <table border="0" cellspacing="0" cellpadding="0" class="table_width_100" width="100%" style="max-width: 680px; min-width: 300px;">
            <tr><td>
            <!-- padding --><div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            <!--header -->
            <tr><td align="center" bgcolor="#ffffff">
                <!-- padding --><div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
                <table width="90%" border="0" cellspacing="0" cellpadding="0">
                    <tr><td align="center">
                        <a href="https://fasttestnow.com/" target="_blank" style="color: #596167; font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
                            <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#596167">
                                <img src="https://fasttestnow.health/images/fast.png" width="135" alt="FastTestNow" border="0" style="display: block;" /></font></a>
                        </td>
                    </tr>
                </table>
                <!-- padding --><div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
            </td></tr>
            <!--header END-->
        
            <!--goods -->
            <tr><td align="center" bgcolor="#ffffff">
                <table width="90%" border="0" cellspacing="0" cellpadding="0">
                    <tr><td align="center">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr><td align="center">
        
                                <div class="mob_100" style="float: left; display: inline-block; width: 100%;">
                                    <table class="mob_100" width="100%" border="0" cellspacing="0" cellpadding="0" align="left" style="border-collapse: collapse;">
                                        <tr><td align="center" style="line-height: 14px; padding: 0 25px;">
                                            <div style="border-width: 1px; border-style: solid; border-color: #eff2f4;background-color: #dbdee3;">
                                                <font face="Arial, Helvetica, sans-serif" size="5" color="#57697e" style="font-size: 22px;">
                                                    <span style="font-family: Arial, Helvetica, sans-serif; font-size: 19px; color: #2a333d;line-height: 1.5;font-weight: bold;">
                                                        Results ' . $pdf_name . ' 
                                                    </span></font>
                                                <div style="line-height: 21px; padding: 0 5px;">
                                                    <font face="Arial, Helvetica, sans-serif" size="3" color="#2e2b2b" style="font-size: 15px;font-weight: bold;">
                                                    <table style="width: 100%; border-collapse: collapse;"><tr style="background-color: #ffffff;">
                                                        <td style="line-height: 2;padding-left: 5px;"> Date & Time Released</td>
                                                        <td style="line-height: 2;padding-left: 5px;">' .  $data['handled_at'] . '</td>
                                                    </tr><tr style="background-color: #e7e9eb;">
                                                        <td style="line-height: 2;padding-left: 5px;">Patient Name</td>
                                                        <td style="line-height: 2;padding-left: 5px;">' . $data['firstname'] . ' ' . $data['lastname'] . '</td>
                                                    </tr></table></font>
                                                </div>
                                                <!-- padding --><div style="height: 5px; line-height: 5px; font-size: 10px;">&nbsp;</div>
                                            </div>
                                            <!-- padding --><div style="height: 50px; line-height: 50px; font-size: 10px;">&nbsp;</div>
                                        </td></tr>
                                    </table>
                                </div>
                            </td></tr>			
                        </table>
        
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height: 14px; padding: 0 25px;">
                            <tbody><tr><td style="width: 14%;">
                                <font face="Arial, Helvetica, sans-serif" size="5" color="#57697e" style="font-size: 22px;">
                                <span style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #2a2929; font-weight: bold;">
                                    Results:
                                </span></font>
                                <!-- padding --><div style="height: 60px; line-height: 60px; font-size: 10px;">&nbsp;</div>
                            </td><td>
                                <a href="' . $data['pdfLink'] . '" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold;">
                                    <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#0074fe">
                                    Download Submission PDF	</font></a>
                                <!-- padding --><div style="height: 60px; line-height: 60px; font-size: 10px;">&nbsp;</div>
                            </td></tr>			
                            </tbody>
                        </table>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height: 14px; padding: 0 25px;">
                            <tbody><tr><td style="width: 14%;">
                                <font face="Arial, Helvetica, sans-serif" size="5" color="#57697e" style="font-size: 22px;">
                                <span style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #2a2929; font-weight: bold;">
                                    User Portal:
                                </span></font>
                                <!-- padding --><div style="height: 60px; line-height: 60px; font-size: 10px;">&nbsp;</div>
                            </td><td>
                                <a href="https://fasttestnow.health/user_portal/login.php?email=' . $receiver . '" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold;">
                                    <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#0074fe">
                                    Click User Portal link	</font></a>
                                <!-- padding --><div style="height: 60px; line-height: 60px; font-size: 10px;">&nbsp;</div>
                            </td></tr>			
                            </tbody>
                        </table>
                        <div style="text-align: left; padding: 10px 25px;">
                            <font face="Arial, Helvetica, sans-serif" size="3" color="#717171" style="font-size: 27px;">
                            <span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold; color: #2a2929;">
                                Medical:
                            </span></font>
                        </div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height: 14px; padding: 0 25px;">
                            <tbody><tr><td style="width: 6%; padding-right: 5px; border-right: 1px solid #ccc;">
                                <a href="https://fasttestnow.com/" target="_blank" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
                                    <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#0074fe">
                                    FDA	</font>
                            </td><td style="padding-left: 5px;">
                                <a href="https://fasttestnow.com/" target="_blank" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
                                    <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#0074fe">
                                    Factsheet for patients	</font></a>
                            </td></tr>			
                            </tbody>
                        </table>
                        <!-- padding --><div style="height: 60px; line-height: 60px; font-size: 10px;">&nbsp;</div>
                        <div style="text-align: left; padding: 10px 25px;">
                            <font face="Arial, Helvetica, sans-serif" size="3" color="#717171" style="font-size: 27px;">
                            <span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold; color: #2a2929;">
                                Kind regards,
                            </span></font>
                        </div>
                        <!-- padding --><div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div>
                        <div style="text-align: left; padding: 10px 25px;">
                            <font face="Arial, Helvetica, sans-serif" size="3" color="#717171" style="font-size: 27px;">
                            <span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold; color: #2a2929;">
                                FastTestNow&#174; Team
                            </span></font>
                        </div>
                        <div style="text-align: left; padding: 0px 25px;">
                            <a href="https://fasttestnow.com/" target="_blank" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
                                <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#0074fe">
                                https://fasttestnow.com/	</font></a>
                        </div>
                    </td></tr>
                    <tr><td><!-- padding --><div style="height: 10px; line-height: 10px; font-size: 10px;">&nbsp;</div></td></tr>
                </table>		
            </td></tr>
            <!--goods END-->
        
            <!--pre-footer -->
            <tr><td class="iage_footer" align="center" bgcolor="#fcfafb" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #e8e8e8;">
                <!-- padding --><div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
                
                <table width="90%" border="0" cellspacing="0" cellpadding="0">
                    <tr><td style="padding: 0 25px;">
                        <div style="line-height: 27px;">
                            <font face="Arial, Helvetica, sans-serif" size="3" color="#0b0909" style="font-size: 12px;">
                            <span style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #0b0909;">
                                IMPORTANT: The information contained in this transmission contains privileged and confidential information, including patient information protected by federal and state privacy laws. It is intended only for the use of the person(s) named above. If you are not the intended recipient, you are hereby notified that any review, dissemination, distribution, or duplication of this communication is strictly prohibited. If you are not the intended recipient, please contact the sender by reply email and destroy all copies of the
        original message. Do NOT forward this e-mail message. Please be aware that e-mail communication can be intercepted in transmission or misdirected. Please consider communicating information by telephone, fax, or surface mail.
                            </span></font>
                        </div>
                    </td></tr>
                </table>
                
                <!-- padding --><div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>	
            </td></tr>
            <!--pre-footer END-->
        
            <!--footer -->
            <tr><td class="iage_footer" align="center" bgcolor="#fcfafb" style="border-top-width: 1px; border-top-style: solid; border-top-color: #ffffff;">
                <!-- padding --><div style="height: 30px; line-height: 30px; font-size: 10px;">&nbsp;</div>	
                
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr><td align="center">
                        <font face="Arial, Helvetica, sans-serif" size="3" color="#717171" style="font-size: 13px;">
                        <span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #717171;">
                            ' . date('Y') . ' &copy; FastTestNow. ALL Rights Reserved.
                        </span></font>				
                    </td></tr>			
                </table>
                
                <!-- padding --><div style="height: 30px; line-height: 30px; font-size: 10px;">&nbsp;</div>	
            </td></tr>
            <!--footer END-->
            <tr><td>
            <!-- padding --><div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
            </td></tr>
        </table>
        <!--[if gte mso 10]>
        </td></tr>
        </table>
        <![endif]-->
         
        </td></tr>
        </table>
                    
        </div> 
        </body>
        </html>';
        // Send an email:
        $postmark = new Postmark("d1082cae-330f-4dec-a6f7-75c2afb80081", "result@fasttestnow.health");
        $result = $postmark
            ->to($receiver)
            ->subject($subject)
            ->plain_message("You've received an encrypted message from FastTestNow&#174;")
            ->html_message($message)
            ->attachment('tests.pdf', base64_encode(file_get_contents($URL)), 'application/pdf')
            ->send();
        //    Delete PDF from Server Folder
        unlink($URL);
        return $result;
    }
}