<?php
error_reporting(0);
include __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;


include('includes/functions.php');
$con = dbCon();
$id = $_GET['id'];
//Initial S3 Client
$client = new S3Client([
    'credentials' => [
        'key' => 'AKIAW7ZPM2DM4VGHHSFN',
        'secret' => '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'
    ],
    'region' => 'us-east-1',
    'version' => 'latest',
]);

$queryObj = mysqli_query($con, "SELECT * FROM tbl_appointment WHERE id=$id");

if (mysqli_num_rows($queryObj) > 0) {
    $fo = mysqli_fetch_assoc($queryObj);
    $patient_email = $fo['email'];
    
    $tokQuery = mysqli_query($con, "SELECT user_token FROM tbl_report WHERE patient_email=$patient_email");
    $tokRes = mysqli_fetch_assoc($tokQuery);

    $type_id = $fo['type_id'];
    $sample_type = 'Nasophar';
    $sample_taken = $fo['sample_collected_date_formatted'];
    $patient_firstname = $fo['firstName'];
    $patient_lastname = $fo['lastName'];
    $patient_birth = $fo['dob'];
    $patient_gender = $fo['gender'];
    $patient_phone = $fo['phone'];
    $patient_passport = $fo['passport_no'];
    $address = $fo['address'];
    $ethnicity = $fo['ethnicity'];
    $released = $fo['released'];
    $appointment_type = $fo['appointment_type'];
    $pdfFileName = $fo['pdf_file_name'];
    $updated_at = $fo['updated_at'];
    function random19()
    {
        $number = "";
        for ($i = 0; $i < 19; $i++) {
            $min = ($i == 0) ? 1 : 0;
            $number .= mt_rand($min, 9);
        }
        return $number;
    }

}

$awsPdfFile = null;
$output = "uploads/8625a3599232039a77533b31ba47469cpdf/66c0300281470a91a1062263462f82e9requestform" . $id . ".pdf";
$awsPdfFile = "66c0300281470a91a1062263462f82e9requestform" . $id . ".pdf";


//check if file is exist and hit this file form download button then directly download file for user
// if (isset($_GET['report_type']) && $_GET['report_type'] == 'D') {
//     try {
//         $result = $client->doesObjectExist(
//             'fasttestnowreports',
//             'REPORTS/' . $awsPdfFile
//         );
//         if ($result == '1') {
//             $file_url = 'https://fasttestnowreports.s3.amazonaws.com/REPORTS/' . $awsPdfFile;
// //            header("Location: " . $file_url);
//             header('Content-Type: application/octet-stream');
//             header("Content-Transfer-Encoding: Binary");
//             header("Content-disposition: attachment; filename=\"" . $awsPdfFile . "\"");
//             echo file_get_contents($file_url);
//             die;
//         }
//     } catch (S3Exception $e) {
//         echo $e->getMessage() . "\n";
//     }
// }


//Delete AWS file
if (!empty($pdfFileName)) {
    try {
        //    Delete File
        $result = $client->deleteObject(array(
            'Bucket' => 'fasttestnowreports',
            'Key' => 'REPORTS/' . $pdfFileName,
        ));
    } catch (S3Exception $e) {
        echo $e->getMessage() . "\n";
    }
}

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'margin_left' => 0,
    'margin_right' => 0,
    'margin_top' => 0,
    'margin_bottom' => 10,
    'margin_header' => 5,
    'margin_footer' => 10,
    'format' => 'A4',
    'default_font' => 'Roboto-Regular',
    'debug' => true

]);
$pdfcontent = '';
$pdfcontent .= '<style>
body{
    font-family: helvetica;
    font-size: 12px;
}
.results {
    width: 120px;
}
.barcode {
	padding: 1.5mm;
	margin: 0;
	vertical-align: top;
	color: #000000;
}
.barcodecell {
	text-align: center;
	vertical-align: middle;
	padding: 0;
}
.logo {
    width: 170px;
    margin-left: 40px;
}
.block {
    margin-right: 40px;
    margin-left: 40px;
    z-index: 400;
}
.header {
    height:350px; 
    width: 100%; 
    vertical-align: bottom; 
    background-color:#dad7e3;
}
.main_text {
    font-size: 18px;
}
@page {
    /* background: url("' . __DIR__ . '/images/fast.png") no-repeat 300px 100px; */
}
 .reqform-cont{
   width: 90%;
   margin: 0 auto;
   font-family: helvetica;
 }
 table {
     font-size: 12px;
 }
 
td { white-space:pre-line }

  tr.bd-left td {
   border-bottom:1px solid #A9A9A9;margin-top: 6px;
  }
 .bd-right {
   width:75%;border-bottom:1px solid #A9A9A9;margin-top: 6px;
  }
 .bdr-left {
   border-bottom:1px solid #A9A9A9;margin-top: 6px;
  }
 .bdr-right {
   border-bottom:1px solid #A9A9A9;margin-top: 6px;
  }
 tr.border-full td {
        border-bottom: 1pt solid #A9A9A9;margin-top: 6px;
      }
.myhr {
    width: 40%;
     padding: 0px;
     margin-bottom: 0px;
     margin-left:0px;
    display: block;
    border : black;
    unicode-bidi: isolate;
    margin-block-start: 10px;
    margin-block-end: 0px;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    overflow: hidden;
    border-style: inset;
    border-width: 1px;
  }
</style>

<body>';
$mpdf->SetFont("Roboto-Regular");
$queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
if (mysqli_num_rows($queryObj) > 0) {
    $reportInfo = mysqli_fetch_assoc($queryObj);
}

    // qr code part;
    $url = 'QRcode/Visby' . $id . '.pdf';
    //generate_qrcode_report($type_id, $url, $fo, $id);
    $queryObj = mysqli_query($con, "SELECT * FROM tbl_appointment WHERE id=$id");
    if (mysqli_num_rows($queryObj) > 0) {
        $reportInfo = mysqli_fetch_assoc($queryObj);
    
        $getyear = explode("/", $reportInfo['dob']);
        if(empty($getyear[2])){
           $getyear[2] = substr($getyear[0],-4); 
        } elseif(strlen($getyear[2]) == 2){
            $getyear[2] = '19'.$getyear[2];
        }
        $dob = date('Y') - $getyear[2];
        
    }

    $pdfcontent .= '<br>
    <table width="70%" align="center" cellspacing = "5">
        <tr class = "bd-left">
        <td width = "30%">
           <p><b>Fast Test Now</b></p>
            </td>
            <td width = "30%" style = "border: none !important;"></td>
            <td width = "30%">Patient: '. $reportInfo['firstName'] . ' '.$reportInfo['lastName'] .'</td>
        </tr>
        <tr class = "bd-left">
            <td >2067 NE 163rd Street</td>
            <td style = "border: none !important;"></td>
            <td >DOB: '. $reportInfo['dob'] .'</td>
        </tr>
        <tr class = "bd-left">
            <td >Miami, FL 33175</td>
            <td style = "border: none !important;"></td>
            <td >Sex: '. $reportInfo['gender'] .'</td>
        </tr>    
        <tr class = "bd-left">
            <td >+1 8338308383</td>
            <td style = "border: none !important;"></td>
            <td >Age: '. $dob .'</td>
        </tr>
        <tr class = "bd-left">
            <td style = "border: none !important;">&nbsp;</td>
            <td style = "border: none !important;"></td>
            <td>State of Residence:</td>
        </tr>
    </table>';

$pdfcontent .= '<div class = "reqform-cont">';

$pdfcontent .= '<h1>Requisition Form</h1><p><b>Molecular Diagnostic Test
</b></p>';
$pdfcontent .= '<table width = "100%"><tr class="border-full"><td width="70%">Test Requested:</div></td><td align="right"><b>[TYPE OF APPOINTMENT BOOKED]</b></td></tr></table><p>Product Description:</p>';

$pdfcontent .= '<table width="100%" cellpadding = "5" cellspacing = "5">
    <tr>
        <th align="left">Patient</th>
        <th align="left">Test Order</th>
        <th align="left">Sample</th>
    </tr>
    <tr class = "bd-left">
        <td width = "30%">First Name: '. $reportInfo['firstName'] .'</td>
        <td width = "32%">CPT/Procedure Code: </td>
        <td width = "30%">Patient ID: '. $_GET['patient_id'].'</td>
    </tr>
    <tr class = "bd-left">
        <td >Last Name: '. $reportInfo['lastName'] .'</td>
        <td style = "border: none !important;">Test Explanation:</td>
        <td>Collection Date: '. $reportInfo['sample_collected_date_formatted'] .'</td>
    </tr>
    <tr class = "bd-left">
        <td >DOB: '. $reportInfo['dob'] .'</td>
        <td style = "border: none !important;"></td>
        <td style = "border: none !important;">Sample Type:&nbsp;&nbsp;&nbsp;&nbsp;<b>Nasopharyngeal</b></span></td>
    </tr>    
    <tr class = "bd-left">
        <td >Gender: '. $reportInfo['gender'] .'</td>
        <td style = "border: none !important;"><b>Diagnosis:</b></td>
        <td style = "border: none !important;"></td>
    </tr>
     <tr class = "bd-left">
        <td >Race: </td>
        <td >ICD-10/Diagnoses Code: Z20.828</td>
        <td style = "border: none !important;"></td>
    </tr> 
   <tr class = "bd-left">
        <td >Ethnicity: '. $reportInfo['ethnicity'] .'</td>
        <td style = "border: none !important;"></td>
        <td style = "border: none !important;"></td>
    </tr> 
    <tr class = "bd-left">
        <td >
        Address: '. $reportInfo['address'] . ',' . $reportInfo['city'] .','.$reportInfo['zipcode'].'</td>
        <td style = "border: none !important;"></td>
        <td style = "border: none !important;"></td>
    </tr> 
    <tr class = "bd-left">
        <td >Phone: '. $reportInfo['phone'] .'</td>
        <td style = "border: none !important;"></td>
        <td style = "border: none !important;"></td>
    </tr> 
    <tr class = "bd-left">
        <td >Email: '. $reportInfo['email'] .'</td>
        <td style = "border: none !important;"></td>
        <td style = "border: none !important;"></td>
    </tr> 
    </table>';
    
$pdfcontent .= '<h4>Ordering Provider: </h4>';    

$pdfcontent .= '<br><table width="50%" cellpadding = "5" cellspacing = "5">
        <tr class="bd-left">
            <td >Practice:</td>
            <td align = "right"><b>Fast Test Now</b></td>
        </tr>
        <tr class="bd-left">
            <td >Name:</td>
            <td align = "right"><b>Dr. Dominique Michelle M.D.</b></td>
        </tr>
        <tr class="bd-left">
            <td >NPI:</td>
            <td align = "right"><b>1235588476</b></td>
        </tr>    
        <tr class="bd-left">
            <td style = "border: none !important;">Address:</td>
            <td align = "right" style = "border: none !important; font-size: 11px;"><b> 2067 NE 163rd Street, North Miami Beach, FL 33162</b></td>
        </tr>
    </table>';    
    
$pdfcontent .= '<table width="100%" cellpadding = "5" cellspacing = "5">
        <tr>
            <td width = "50%">By signing below, I, as the ordering Medical Provider, certify that the patient has been informed of the benefits and limitations of the laboratory test requested, and has had the opportunity to have all questions answered adequately.</td>
            <td align = "center"><img src="' . __DIR__ . '/images/fasttestsign.png" style="width: 350px; margin-top: -80px;"/></td>
        </tr>
    </table>';        
    
$pdfcontent .= '<table width="100%" cellpadding = "5" cellspacing = "5">
        <tr>
            <td width = "50%"></td>
            <td width = "20%" align = "left" style = "text-decoration:underline;">Signed at [DATE AND TIME OF PURCHASE]<br>
            <span style = "text-decoration: none !important;">Dr. Dominique Michelle M.D.</span>
            </td>
        </tr>
        <tr>
    </table>';     
    
$pdfcontent .= '<p style = "font-weight: bold; margin-top: -20px; margin-left: 10ox; font-size: 14px;">Fast Test Now</p>';

$pdfcontent .= '</div>';

//echo $pdfcontent; exit;

$pdfcontent .= '</body>';

//echo $pdfcontent; exit;

$mpdf->WriteHTML($pdfcontent);
//$mpdf->Output();exit;
//output in browser
$mpdf->Output($output, 'F');
$pdf_file_name = '';
$pdf_file_url = '';
//Upload File
try {
    //    Upload File
    $result = $client->putObject([
        'Bucket' => 'fasttestnowreports',
        'Key' => 'REPORTS/' . $awsPdfFile,
        'SourceFile' => $output
    ]);
    if ($result["@metadata"]["statusCode"] == '200') {
        $pdf_file_url = $result["ObjectURL"];
        $pdf_file_name = basename($result["ObjectURL"]);
        $q = mysqli_query($con, "UPDATE tbl_appointment SET pdf_file_url='" . $result["ObjectURL"] . "',pdf_file_name='" . $pdf_file_name . "' WHERE id=$id");
        unlink($output);
    }
} catch (S3Exception $e) {
    echo($e->getMessage());
}

if (isset($_GET['report_type'])) {
    if ($_GET['report_type'] == 'D') {
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $pdf_file_name . "\"");
        echo file_get_contents($pdf_file_url);
        die;
    }
}

?>
