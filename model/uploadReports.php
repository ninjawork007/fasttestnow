<?php

// include('../includes/functions.php');
include('../model/uploadFileOnAwsS3.php');

class UploadReports {

    private $data;

    function __construct($entities) {
        $this->data = $entities;
    }
    function create() {
        $conn = dbCon();
		$sql = "CREATE TABLE tbl_uploads (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            appointment_id INT(11) NOT NULL,
            `type_id` INT(4) NOT NULL,
            user_token VARCHAR(30) NOT NULL,
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
        $val = mysqli_query($conn,"SELECT * FROM tbl_uploads LIMIT 1");
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
            $sql = "INSERT INTO tbl_uploads (`".$params."`) VALUES ('".$importData."')";
            
            $q = mysqli_query($conn, $sql);
            $id = mysqli_insert_id($conn);
            if ($q === true) {
                $sql = "UPDATE tbl_uploads SET `user_token`='".$this->userToken($id)."' WHERE id=". $id;
                $t = mysqli_query($conn, $sql);
                if ($t === true) {
                    return array('id' => $id, 'result' => $q);
                }
            } else {
                return array('id' => 0, 'result' => (boolean) false);
            }
        } else {
            var_dump("Failed in Creating tbl_uploads Table"); die;
        }
    }

    function userToken($id) {
        $conn = dbCon(); 
        $maxTokenQ = "SELECT user_token FROM `tbl_uploads` WHERE id NOT IN ($id) ORDER BY id DESC LIMIT 0,1";
        $maxResult = mysqli_query($conn, $maxTokenQ);
        $token = mysqli_fetch_row($maxResult);
        $userToken = (int)$token[0];
        $newUserToken = str_pad($userToken + 1, 16, '0', STR_PAD_LEFT);

        return $newUserToken;
    }

    function update($id = NULL) {
        $conn = dbCon(); 
        
        $sql = "UPDATE tbl_uploads SET ".$this->data." WHERE id=". $id;
        
        $q = mysqli_query($conn, $sql);
        if ($q === true) 
            return array('id' => $id, 'result' => $q);
        else 
            return array('id' => 0, 'result' => (boolean) false);
    }

    function delete($id = NULL) {
        $conn = dbCon();

        $sql = "DELETE FROM tbl_uploads WHERE id=".$id;
        $q = mysqli_query($conn, $sql);

        if ($q === true) 
            return array('id' => $id, 'result' => $q);
        else 
            return array('id' => 0, 'result' => (boolean) false);
    }

    function findById($id = NULL) {
        $conn = dbCon();

        $sql = "SELECT * FROM  tbl_uploads WHERE id=".$id;
        $q = mysqli_query($conn, $sql);
        $data = mysqli_fetch_assoc($q);
        
        return $data;
    }

    function findAll() {
        $conn = dbCon();

        $sql = "SELECT * FROM  tbl_uploads";
        $q = mysqli_query($conn, $sql);
        $data = array();
        while (($row = mysqli_fetch_assoc($q))) {
            $data[] = $row;
        }

        return $data;
    }

    // pdf manipulation
    function generatePDF($type_id, $output, $id) {

        $pdf_name = "";
        // upload the pdf on AWS S3
        $awsPdfFile = "";

        switch($type_id) {
            case 1:
                $pdf_name = "Visby PCR";
                $awsPdfFile = "66c0300281470a91a1062263462f82e9pcr_" .$type_id ."_". $id . ".pdf";
            break;
            case 2:
                $pdf_name = "Antigen";
                $awsPdfFile = "b8af05afc5dbf0a9cbe3c02ab2962f0aantigen_".$type_id ."_". $id . ".pdf";
            break;
            case 3:
                $pdf_name = "Accula Rt-PCR";
                $awsPdfFile = "c742a402367f2030636af25ce0c9713eaccula".$type_id ."_". $id . ".pdf";
            break;
            case 4:
                $pdf_name = "Antibody Screening";
                $awsPdfFile = "8513da060dea559ed3dff467ecb2651eantibody_".$type_id ."_". $id . ".pdf";
            break;
            case 5:
                $pdf_name = "Flu";
                $awsPdfFile = "8513da060dea559ed3dff467ecb2651eaflu_".$type_id ."_". $id . ".pdf";
            break;
            case 6:
                $pdf_name = "COMPOUNDED semaglutide RX";
                $awsPdfFile = "8513da060dea559ed3dff467ecb2651eoxorder_".$type_id ."_". $id . ".pdf";
            break;
            case 7:
                $pdf_name = "Mono Screening";
                $awsPdfFile = "8513da060dea559ed3dff467ecb2651emono_".$type_id ."_". $id . ".pdf";
            break;
            case 8:
                $pdf_name = "Strep Screening";
                $awsPdfFile = "8513da060dea559ed3dff467ecb2651estrep_".$type_id ."_". $id . ".pdf";
            break;
            case 9:
                $pdf_name = "Syphilis Screening";
                $awsPdfFile = "8513da060dea559ed3dff467ecb2651esyphilis_".$type_id ."_". $id . ".pdf";
            break;
            case 10:
                $pdf_name = "Hemoglobin Screening";
                $awsPdfFile = "8513da060dea559ed3dff467ecb2651ehemoglobin_".$type_id ."_". $id . ".pdf";
            break;
            case 11:
                $pdf_name = "Hiv Screening";
                $awsPdfFile = "8513da060dea559ed3dff467ecb2651ehiv_".$type_id ."_". $id . ".pdf";
            break;
            case 12:
                $pdf_name = "Rsv Screening";
                $awsPdfFile = "8513da060dea559ed3dff467ecb2651ersv_".$type_id ."_". $id . ".pdf";
            break;
            case 13:
                $pdf_name = "Thyroid Screening";
                $awsPdfFile = "8513da060dea559ed3dff467ecb2651ethyroid_".$type_id ."_". $id . ".pdf";
            break;
        }
                
        

        $url = $this->uploadReportToAwsS3($output, $awsPdfFile);
        
        $conn = dbCon();
        $sql = "UPDATE tbl_uploads SET pdfLink='".$url."' WHERE id=". $id;
        $q = mysqli_query($conn, $sql);
        
        if($q) {
            return $this->sendEmail($this->findById($id), $output, $pdf_name);
        }
    }

    function uploadReportToAwsS3($localPath, $awsPath) {
        $sourceFile = $localPath;
        $s3Link = $awsPath;
        

        $awsS3 = new UploadFileOnAwsS3Client($sourceFile);
        $result = $awsS3->upload($s3Link);
        return $result;
    }

    function sendEmail($data, $URL, $pdf_name) {
        
        require_once("../libraries/postmark.php");

        $receiver = $data['email'];
        $pdf_name = $pdf_name;
        $subject = $pdf_name . " -" . $data['handled_at'];
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