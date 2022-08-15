<?php
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';

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

$queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
if (mysqli_num_rows($queryObj) > 0) {
    $fo = mysqli_fetch_assoc($queryObj);
    $type_id = $fo['type_id'];
    $test_location = $fo['location'];
    $sample_taken = $fo['sample_taken'];
    $patient_firstname = $fo['patient_firstname'];
    $patient_lastname = $fo['patient_lastname'];
    $patient_birth = $fo['patient_birth'];
    $patient_email = $fo['patient_email'];
    $patient_gender = $fo['patient_gender'];
    $patient_phone = $fo['patient_phone'];
    $patient_passport = $fo['patient_passport'];
    $report_results = $fo['report_results'];
    $report_updated_at = $fo['report_updated_at'];
    $released = $fo['released'];
    $patient_test_brand = $fo['antigen_test_brand'];
    $pdfFileName = $fo['pdf_file_name'];
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
if ($type_id == 1) {
    $output = "uploads/8625a3599232039a77533b31ba47469cpdf/66c0300281470a91a1062263462f82e9pcr" . $id . ".pdf";
    $awsPdfFile = "66c0300281470a91a1062263462f82e9pcr" . $id . ".pdf";
}
if ($type_id == 2) {
    $output = "uploads/8625a3599232039a77533b31ba47469cpdf/b8af05afc5dbf0a9cbe3c02ab2962f0aantigen" . $id . ".pdf";
    $awsPdfFile = "b8af05afc5dbf0a9cbe3c02ab2962f0aantigen" . $id . ".pdf";
}
if ($type_id == 3) {
    $output = "uploads/8625a3599232039a77533b31ba47469cpdf/c742a402367f2030636af25ce0c9713eaccula" . $id . ".pdf";
    $awsPdfFile = "c742a402367f2030636af25ce0c9713eaccula" . $id . ".pdf";
}
if ($type_id == 4) {
    $output = "uploads/8625a3599232039a77533b31ba47469cpdf/8513da060dea559ed3dff467ecb2651eantibody" . $id . ".pdf";
    $awsPdfFile = "8513da060dea559ed3dff467ecb2651eantibody" . $id . ".pdf";
}
if ($type_id == 5) {
    $output = "uploads/8625a3599232039a77533b31ba47469cpdf/8513da060dea559ed3dff467ecb2651eaflu" . $id . ".pdf";
    $awsPdfFile = "8513da060dea559ed3dff467ecb2651eantibody" . $id . ".pdf";
}
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

$gender = ($patient_gender == 0) ? "Male" : "Female";
$result = ($report_results == 0) ? "Negative" : "Positive";
$result_img = ($report_results == 0) ? "assets/images/negative.png" : "assets/images/positive.png";
$antibody_result = ($report_results == 0) ? "NOT DETECTED" : "DETECTED";

$updated_at = date('F d Y', strtotime($report_updated_at));

// Include mpdf library file
include('qrcode.php');

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
$pdfcontent = '';
$pdfcontent .= '<style>
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

</style>

<body>';
$mpdf->SetFont("Roboto-Regular");
$queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
if (mysqli_num_rows($queryObj) > 0) {
    $reportInfo = mysqli_fetch_assoc($queryObj);
}

if ($type_id == 1) {
    // qr code part;
    $url = 'QRcode/Visby' . $id . '.pdf';
    generate_qrcode_report($type_id, $url, $fo, $id);
    $queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
    if (mysqli_num_rows($queryObj) > 0) {
        $reportInfo = mysqli_fetch_assoc($queryObj);
    }
    $pdfcontent .= '<table width="100%"  class="header">
        <tr><td width="33%">&nbsp;</td></tr>
        <tr><td width="33%">&nbsp;</td></tr>
        <tr>
            <td width="33%"></td>
            <td style="text-align: left;vertical-align:left;">Patient ID: ' . $reportInfo['user_token'] . '</td>
        </tr>
        <tr>
        <td width="37%;"><input type="image" class="logo" src="' . __DIR__ . '/images/fast.png" /></td>
        
        <td style="text-align: left;vertical-align:middle;">
            <h2>Sars-Cov-2 Rt-PCR Screening Results</h2>
        </td>
        </tr>
        <tr><td width="33%">&nbsp;</td></tr>
        </table>';

    $pdfcontent .= '<table border="0" class="block main_text" style="width: 100%;" >';
    $pdfcontent .= '<tr><td>&nbsp;</td></tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;"  colspan="2"><b>Date & Time Sample Collected</b></td>';
    $pdfcontent .= '<td style="width: 50%;"  colspan="2"><b>Date & Time Released</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
    $pdfcontent .= $sample_taken;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
    $pdfcontent .= $released;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;"  colspan="2"><b>Patient Name</b></td>';
    $pdfcontent .= '<td style="width: 50%;"  colspan="2"><b>Date of Birth</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $patient_firstname . ' ' . $patient_lastname . '</td>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $patient_birth . '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr >';
    $pdfcontent .= '<td style="width: 10%;height: 50px">';
    $pdfcontent .= '<b>Gender</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align:left;color:#6a7180">';
    $pdfcontent .= $gender;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%" >';
    $pdfcontent .= '<b>Email</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180;text-align:left">';
    $pdfcontent .= $patient_email;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%; height: 50px" >';
    $pdfcontent .= '<b>Passport</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center" >';
    $pdfcontent .= $patient_passport;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 25%" >';
    $pdfcontent .= '<b>Phone Number</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180">';
    $pdfcontent .= $patient_phone;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%" >';
    $pdfcontent .= '<b>Results</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center" >';
    $pdfcontent .= '<input type="image" class="results" src="' . $result_img . '" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 25%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table border="0"  class="block" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 100%;">';
    $pdfcontent .= '<hr>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= '<b>Sample Type:</b> Nasopharyngeal Swab&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b> Test manufacturer/kit:</b> bioteke corporation';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" class="second_text">';
    $pdfcontent .= "<b>Sample processed by:</b><b> Pinnacle Genetics Labs Inc</b> using rt-pcr test kit: <u style='color:blue;'>Factsheet for patients</u><br>";
    $pdfcontent .= "<label style='font-size:13px;'>The United States FDA has made this test available under an emergency access mechanism called an Emergency Use Authorization";
    $pdfcontent .= "(EUA). The EUA is supported by the Secretary of Health and Human Service's (HHS's) declaration that circumstances exist to justify the";
    $pdfcontent .= "emergency use of in vitro diagnostics (IVDs) for the detection and/or diagnosis of the virus that causes COVID-19. EUA# EUA203089</label>";
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" style="padding-top: 20px;">';
    $pdfcontent .= '<b>If negative results:</b> This may mean you were not infected at the time your test was performed. This';
    $pdfcontent .= 'does not mean you will not get infected or sick. It is possible that you could be exposed later and then';
    $pdfcontent .= 'develop the illness. A negative test result does not rule out getting sick later. It is still strongly advised';
    $pdfcontent .= 'that you monitor your health, wear a mask, and practice social distancing and proper hygiene.';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table  class="block" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" style="font-size: 20px;">';
    $pdfcontent .= '<b>Next Steps</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>1</td>';
    $pdfcontent .= "<td><b>Follow your doctor's orders</b></td>";
    $pdfcontent .= "</tr>";
    $pdfcontent .= '<tr>';
    $pdfcontent .= "<td></td>";
    $pdfcontent .= "<td>Follow your medical professional's advice to determine your course of action.</td>";
    $pdfcontent .= "</tr>";
    $pdfcontent .= "<tr>";
    $pdfcontent .= "<td>2</td>";
    $pdfcontent .= '<td><b>Maintain social distance</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Stay home from work, school, and all activities when you have any COVID-19 symptoms. Keep';
    $pdfcontent .= 'away from others who are sick and limit close contacts as much as possible (about 6 feet).</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>3</td>';
    $pdfcontent .= '<td><b>Wear a mask</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Wear a facial covering over your mouth and nose when you are unable to socially distance.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>4</td>';
    $pdfcontent .= '<td><b>Wash your hands</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Clean your hands often, either with soap and water for 20 seconds or with a hand sanitizer that';
    $pdfcontent .= 'contains at least 60% alcohol.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>5</td>';
    $pdfcontent .= '<td><b>Learn more</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>COVID-19 is in a family of viruses known as coronaviruses. To learn more about COVID-19 and';
    $pdfcontent .= 'how you can help reduce the spread of the virus in your community, please check CDC website.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr><td>&nbsp;</td></tr>';

    $pdfcontent .= '</table>';
    $pdfcontent .= '<table  class="block" border="0" style="width: 100%;margin-bottom: 50px; font-size: 12px;border-top: 0.1px solid #333333">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td rowspan="3"  class="barcodecell">';
    $pdfcontent .= '<barcode code="' . $reportInfo['qrcode_file_url'] . '" type="QR" class="barcode" size="1" error="M" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
    $pdfcontent .= '<img src="' . __DIR__ . '/images/sign.jpg" style="width: 90px;"/>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    //$pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
    $pdfcontent .= $test_location;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'Physician Oversight: Dr. Dominique M Thompson #ME134892';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';
}
if ($type_id == 2) {
    $url = 'QRcode/Antigen' . $id . '.pdf';
    generate_qrcode_report($type_id, $url, $fo, $id);
    $queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
    if (mysqli_num_rows($queryObj) > 0) {
        $reportInfo = mysqli_fetch_assoc($queryObj);
    }
    // var_dump($updated_at);die;
    $pdfcontent .= '<table width="100%" class="header">
            <tr><td >&nbsp;</td></tr>
            <tr><td >&nbsp;</td></tr>
            <tr>
            <td width="28%;"><input type="image" class="logo" src="' . __DIR__ . '/images/fast.png" /></td>
            
            <td width="40%;" style="text-align: center;vertical-align:middle;">
                <h2>Rapid Antigen Results</h2>
            </td>
            <td style="text-align: left;vertical-align:middle;">
                <span style="font-size:12px;">Patient ID: &nbsp;&nbsp;&nbsp;&nbsp;' . $reportInfo['user_token'] . '</span> <br/><br/>
                <h3>' . date("D, M d, Y") . '</h3>
            </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
        </table>';

    $pdfcontent .= '<table class="block main_text" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr><td>&nbsp;</td></tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Date & Time Test Taken</b></td>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Date of Birth</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
    $pdfcontent .= $sample_taken;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
    $pdfcontent .= $patient_birth;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Patient Name</b></td>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Email</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $patient_firstname . ' ' . $patient_lastname . '</td>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $patient_email . '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%;height: 50px">';
    $pdfcontent .= '<b>Gender</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center;">';
    $pdfcontent .= $gender;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%; height: 50px" >';
    $pdfcontent .= '<b>Passport</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center" >';
    $pdfcontent .= $patient_passport;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Results</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center;">';
    $pdfcontent .= '<input type="image" class="results" src="' . $result_img . '" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table  class="block" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 100%;">';
    $pdfcontent .= '<hr>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= '<b>Sample Type:</b> Nasopharyngeal Swab';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= "<b>" . $patient_test_brand . "</b> <br>";
    $pdfcontent .= "<u style='color:blue;'>FDA | Factsheet for patients</u>";
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= "<b>Another test kit type: GenBody SARS-CoV-2 Antigen";
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" style="padding-top: 20px;">';
    $pdfcontent .= '<b>If negative results:</b> This may mean you were not infected at the time your test was performed. This
                            does not mean you will not get infected or sick. It is possible that you were early in your infection at the
                            time of your test and that you could test positive later, or you could be exposed later and then develop
                            the illness. A negative test result does not rule out getting sick later. It is still strongly advised that you
                            monitor your health, wear a mask, and practice social distancing and proper hygiene.';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table class="block" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" style="font-size: 20px;">';
    $pdfcontent .= '<b>Next Steps</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>1</td>';
    $pdfcontent .= "<td><b>Follow your doctor's orders</b></td>";
    $pdfcontent .= "</tr>";
    $pdfcontent .= '<tr>';
    $pdfcontent .= "<td></td>";
    $pdfcontent .= "<td>Follow your medical professional's advice to determine your course of action.</td>";
    $pdfcontent .= "</tr>";
    $pdfcontent .= "<tr>";
    $pdfcontent .= "<td>2</td>";
    $pdfcontent .= '<td><b>Maintain social distance</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Stay home from work, school, and all activities when you have any COVID-19 symptoms. Keep';
    $pdfcontent .= 'away from others who are sick and limit close contacts as much as possible (about 6 feet).</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>3</td>';
    $pdfcontent .= '<td><b>Wear a mask</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Wear a facial covering over your mouth and nose when you are unable to socially distance.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>4</td>';
    $pdfcontent .= '<td><b>Wash your hands</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Clean your hands often, either with soap and water for 20 seconds or with a hand sanitizer that';
    $pdfcontent .= 'contains at least 60% alcohol.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>5</td>';
    $pdfcontent .= '<td><b>Learn more</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>COVID-19 is in a family of viruses known as coronaviruses. To learn more about COVID-19 and
                                how you can help reduce the spread of the virus in your community, tap here.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr><td >&nbsp;</td></tr>';
    $pdfcontent .= '</table>';
    $pdfcontent .= '<table  class="block" border="0" style="width: 100%;margin-bottom: 50px; font-size: 12px;border-top: 0.1px solid #333333">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td rowspan="3"  class="barcodecell">';
    $pdfcontent .= '<barcode code="' . $reportInfo['qrcode_file_url'] . '" type="QR" class="barcode" size="1" error="M" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
    $pdfcontent .= '<img src="' . __DIR__ . '/images/sign.jpg" style="width: 90px;"/>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    //$pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
    $pdfcontent .= $test_location;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'Physician Oversight: Dr. Dominique M Thompson #ME134892';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';
}
if ($type_id == 3) {
    $url = 'QRcode/Accula' . $id . '.pdf';
    generate_qrcode_report($type_id, $url, $fo, $id);
    $queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
    if (mysqli_num_rows($queryObj) > 0) {
        $reportInfo = mysqli_fetch_assoc($queryObj);
    }
    $pdfcontent .= '<table width="100%" class="header">
        <tr><td width="33%">&nbsp;</td></tr>
        <tr><td width="33%">&nbsp;</td></tr>
        <tr>
            <td width="33%"></td>
            <td style="vertical-align:middle;">Patient ID: ' . $reportInfo['user_token'] . '</td>
        </tr>
        <tr>
        <td width="37%;"><input type="image" class="logo" src="' . __DIR__ . '/images/fast.png" /></td>
        
        <td style="text-align: left;vertical-align:middle;">
            <h2>Accula Rt-PCR Screening Results</h2>
        </td>
        </tr>
        <tr><td width="33%">&nbsp;</td></tr>
        </table>';

    $pdfcontent .= '<table border="0" class="block main_text" style="width: 100%;">';
    $pdfcontent .= '<tr><td>&nbsp;</td></tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Date & Time Sample Collected</b></td>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Date & Time Released</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
    $pdfcontent .= $sample_taken;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
    $pdfcontent .= $released;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Patient Name</b></td>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Date of Birth</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $patient_firstname . ' ' . $patient_lastname . '</td>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $patient_birth . '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%; height: 50px">';
    $pdfcontent .= '<b>Gender</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%; text-align:left; color:#6a7180">';
    $pdfcontent .= $gender;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Email</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180;text-align:left;">';
    $pdfcontent .= $patient_email;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%; height: 50px">';
    $pdfcontent .= '<b>Passport</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center" >';
    $pdfcontent .= $patient_passport;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 25%">';
    $pdfcontent .= '<b>Phone Number</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180">';
    $pdfcontent .= $patient_phone;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Results</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center" >';
    $pdfcontent .= '<input type="image" class="results" src="' . $result_img . '" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 25%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table class="block" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 100%;">';
    $pdfcontent .= '<hr>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= '<b>Sample Type:</b> Nasopharyngeal Swab';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= "<b>Accula Medical</b> Rt-PCR Test Device: <u style='color:blue;'>FDA | Factsheet for patients</u><br>";
    $pdfcontent .= "<label style='font-size: 13px'>The United States FDA has made this test available under an emergency access mechanism called an Emergency Use Authorization";
    $pdfcontent .= "(EUA). The EUA is supported by the Secretary of Health and Human Service's (HHS's) declaration that circumstances exist to justify the";
    $pdfcontent .= "emergency use of in vitro diagnostics (IVDs) for the detection and/or diagnosis of the virus that causes COVID-19. EUA# EUA203089</label>";
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" style="padding-top: 20px;">';
    $pdfcontent .= '<b>If negative results:</b> This may mean you were not infected at the time your test was performed. This';
    $pdfcontent .= 'does not mean you will not get infected or sick. It is possible that you could be exposed later and then';
    $pdfcontent .= 'develop the illness. A negative test result does not rule out getting sick later. It is still strongly advised';
    $pdfcontent .= 'that you monitor your health, wear a mask, and practice social distancing and proper hygiene.';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table class="block" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" style="font-size: 20px;">';
    $pdfcontent .= '<b>Next Steps</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>1</td>';
    $pdfcontent .= "<td><b>Follow your doctor's orders</b></td>";
    $pdfcontent .= "</tr>";
    $pdfcontent .= '<tr>';
    $pdfcontent .= "<td></td>";
    $pdfcontent .= "<td>Follow your medical professional's advice to determine your course of action.</td>";
    $pdfcontent .= "</tr>";
    $pdfcontent .= "<tr>";
    $pdfcontent .= "<td>2</td>";
    $pdfcontent .= '<td><b>Maintain social distance</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Stay home from work, school, and all activities when you have any COVID-19 symptoms. Keep';
    $pdfcontent .= 'away from others who are sick and limit close contacts as much as possible (about 6 feet).</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>3</td>';
    $pdfcontent .= '<td><b>Wear a mask</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Wear a facial covering over your mouth and nose when you are unable to socially distance.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>4</td>';
    $pdfcontent .= '<td><b>Wash your hands</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Clean your hands often, either with soap and water for 20 seconds or with a hand sanitizer that';
    $pdfcontent .= 'contains at least 60% alcohol.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>5</td>';
    $pdfcontent .= '<td><b>Learn more</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>COVID-19 is in a family of viruses known as coronaviruses. To learn more about COVID-19 and';
    $pdfcontent .= 'how you can help reduce the spread of the virus in your community, please check CDC website.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr><td>&nbsp;</td></tr>';
    $pdfcontent .= '</table>';
    $pdfcontent .= '<table  class="block" border="0" style="width: 100%;margin-bottom: 50px; font-size: 12px;border-top: 0.1px solid #333333">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td rowspan="3" class="barcodecell">';
    $pdfcontent .= '<barcode code="' . $reportInfo['qrcode_file_url'] . '" type="QR" class="barcode" size="1" error="M" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
    $pdfcontent .= '<img src="' . __DIR__ . '/images/sign.jpg" style="width: 90px;"/>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    //$pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
    $pdfcontent .= $test_location;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'Physician Oversight: Dr. Dominique M Thompson #ME134892';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';
}
if ($type_id == 4) {
    $url = 'QRcode/Antibody' . $id . '.pdf';
    generate_qrcode_report($type_id, $url, $fo, $id);
    $queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
    if (mysqli_num_rows($queryObj) > 0) {
        $reportInfo = mysqli_fetch_assoc($queryObj);
    }
    // var_dump($updated_at);die;
    $pdfcontent .= '<table width="100%" class="header">
            <tr><td >&nbsp;</td></tr>
            <tr><td >&nbsp;</td></tr>
            <tr>
            <td width="28%;"><input type="image" class="logo" src="' . __DIR__ . '/images/fast.png" /></td>
            
            <td width="40%;" style="text-align: center;vertical-align:middle;">
                <h2>Rapid Antibody Screening Results</h2>
            </td>
            <td style="vertical-align:middle;">
                <span style="font-size:12px;">Patient ID: &nbsp;&nbsp;&nbsp;&nbsp;' . $reportInfo['user_token'] . '</span> <br/><br/>
                <h3>' . date("D, M d, Y") . '</h3>
            </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
        </table>';
    $pdfcontent .= '<table class="block main_text" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr><td>&nbsp;</td></tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Date & Time Test Taken</b></td>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Date of Birth</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
    $pdfcontent .= $sample_taken;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
    $pdfcontent .= $patient_birth;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Patient Name</b></td>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Email</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $patient_firstname . ' ' . $patient_lastname . '</td>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $patient_email . '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%; height: 50px;">';
    $pdfcontent .= '<b>Gender</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180; text-align:center">';
    $pdfcontent .= $gender;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Passport</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%">';
    // $pdfcontent .= $patient_passport;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Results</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center">';
    $pdfcontent .= $antibody_result;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table class="block" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 100%;">';
    $pdfcontent .= '<hr>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= '<b>Collection Method:</b> Finger Prick';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= "RightSign COVID-19 IgG/IgM Rapid Test Cassette: <br>";
    $pdfcontent .= "<u style='color:blue;'>FDA | Factsheet for patients</u>";
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" style="padding-top: 20px;">';
    $pdfcontent .= '<b>Detected (positive):</b> You produced the COVID-19 IgG antibody and have a high likelihood of prior infection. Some patients with past infections may not have experienced any symptoms. It is unclear at this time if a positive IgG infers immunity against future COVID-19 infection. Please continue with universal precautions: social distancing, hand washing and when applicable PPE such as masks or gloves.';

    $pdfcontent .= '<b>Not Detected (negative):</b> You tested negative for COVID-19 IgG antibody. This means you have not been infected with COVID-19. Please note, it may take 14-21 days to produce detectable levels of IgG following infection. If you had symptoms consistent with COVID-19 within the past 3 weeks and tested negative, repeat testing in 1-2 weeks may yield a positive result.';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table class="block" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" style="font-size: 20px;">';
    $pdfcontent .= '<b>Next Steps</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>1</td>';
    $pdfcontent .= "<td><b>Follow your doctor's orders</b></td>";
    $pdfcontent .= "</tr>";
    $pdfcontent .= '<tr>';
    $pdfcontent .= "<td></td>";
    $pdfcontent .= "<td>Follow your medical professional's advice to determine your course of action.</td>";
    $pdfcontent .= "</tr>";
    $pdfcontent .= "<tr>";
    $pdfcontent .= "<td>2</td>";
    $pdfcontent .= '<td><b>Maintain social distance</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Stay home from work, school, and all activities when you have any COVID-19 symptoms. Keep';
    $pdfcontent .= 'away from others who are sick and limit close contacts as much as possible (about 6 feet).</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>3</td>';
    $pdfcontent .= '<td><b>Wear a mask</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Wear a facial covering over your mouth and nose when you are unable to socially distance.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>4</td>';
    $pdfcontent .= '<td><b>Wash your hands</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Clean your hands often, either with soap and water for 20 seconds or with a hand sanitizer that';
    $pdfcontent .= 'contains at least 60% alcohol.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>5</td>';
    $pdfcontent .= '<td><b>Learn more</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>COVID-19 is in a family of viruses known as coronaviruses. To learn more about COVID-19 and
                                how you can help reduce the spread of the virus in your community, tap here.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr><td >&nbsp;</td></tr>';
    $pdfcontent .= '</table>';
    $pdfcontent .= '<table  class="block" border="0" style="width: 100%;margin-bottom: 50px; font-size: 12px;border-top: 0.1px solid #333333">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td rowspan="3" class="barcodecell">';
    $pdfcontent .= '<barcode code="' . $reportInfo['qrcode_file_url'] . '" type="QR" class="barcode" size="1" error="M" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
    $pdfcontent .= '<img src="' . __DIR__ . '/images/sign.jpg" style="width: 90px;"/>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    //$pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
    $pdfcontent .= $test_location;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'Physician Oversight: Dr. Dominique M Thompson #ME134892';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';
}

if ($type_id == 5) {
    $url = 'QRcode/Flu' . $id . '.pdf';
    generate_qrcode_report($type_id, $url, $fo, $id);
    $queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
    if (mysqli_num_rows($queryObj) > 0) {
        $reportInfo = mysqli_fetch_assoc($queryObj);
    }
    // var_dump($updated_at);die;
    $pdfcontent .= '<table width="100%" class="header">
            <tr><td >&nbsp;</td></tr>
            <tr><td >&nbsp;</td></tr>
            <tr>
            <td width="28%;"><input type="image" class="logo" src="' . __DIR__ . '/images/fast.png" /></td>
            
            <td width="40%;" style="text-align: center;vertical-align:middle;">
                <h2>Flu A/B Screening Results</h2>
            </td>
            <td style="text-align: left;vertical-align:middle;">
                <span style="font-size:12px;">Patient ID: &nbsp;&nbsp;&nbsp;&nbsp;' . $reportInfo['user_token'] . '</span> <br/><br/>
                <h3>' . date("D, M d, Y") . '</h3>
            </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
        </table>';
    $pdfcontent .= '<table class="block main_text" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr><td>&nbsp;</td></tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Date & Time Test Taken</b></td>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Date of Birth</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
    $pdfcontent .= $sample_taken;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">';
    $pdfcontent .= $patient_birth;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Patient Name</b></td>';
    $pdfcontent .= '<td style="width: 50%;" colspan="2"><b>Email</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $patient_firstname . ' ' . $patient_lastname . '</td>';
    $pdfcontent .= '<td style="width: 50%;color:#6a7180" colspan="2">' . $patient_email . '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%;height: 50px">';
    $pdfcontent .= '<b>Gender</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center;">';
    $pdfcontent .= $gender;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%; height: 50px" >';
    $pdfcontent .= '<b>Passport</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center" >';
    $pdfcontent .= $patient_passport;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Results</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;text-align:center;">';
    $pdfcontent .= '<input type="image" class="results" src="' . $result_img . '" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table  class="block" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 100%;">';
    $pdfcontent .= '<hr>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= '<b>Sample Type:</b> Nasopharyngeal Swab';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= "Method: <b>OSOM ULTRA FLU A&B Screening</b> <br>";
    $pdfcontent .= "<u style='color:blue;'>FDA | Factsheet for patients</u>";
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= "<b>Another test kit type: GenBody SARS-CoV-2 Antigen";
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" style="padding-top: 20px;">';
    $pdfcontent .= '<b>If negative results:</b> This may mean you were not infected at the time your test was performed. This
                            does not mean you will not get infected or sick. It is possible that you were early in your infection at the
                            time of your test and that you could test positive later, or you could be exposed later and then develop
                            the illness. A negative test result does not rule out getting sick later. It is still strongly advised that you
                            monitor your health, wear a mask, and practice social distancing and proper hygiene.';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table class="block" border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2" style="font-size: 20px;">';
    $pdfcontent .= '<b>Next Steps</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>1</td>';
    $pdfcontent .= "<td><b>Follow your doctor's orders</b></td>";
    $pdfcontent .= "</tr>";
    $pdfcontent .= '<tr>';
    $pdfcontent .= "<td></td>";
    $pdfcontent .= "<td>Follow your medical professional's advice to determine your course of action.</td>";
    $pdfcontent .= "</tr>";
    $pdfcontent .= "<tr>";
    $pdfcontent .= "<td>2</td>";
    $pdfcontent .= '<td><b>Maintain social distance</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Stay home from work, school, and all activities when you have any COVID-19 symptoms. Keep';
    $pdfcontent .= 'away from others who are sick and limit close contacts as much as possible (about 6 feet).</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>3</td>';
    $pdfcontent .= '<td><b>Wear a mask</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Wear a facial covering over your mouth and nose when you are unable to socially distance.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>4</td>';
    $pdfcontent .= '<td><b>Wash your hands</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>Clean your hands often, either with soap and water for 20 seconds or with a hand sanitizer that';
    $pdfcontent .= 'contains at least 60% alcohol.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td>5</td>';
    $pdfcontent .= '<td><b>Learn more</b></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '<td>COVID-19 is in a family of viruses known as coronaviruses. To learn more about COVID-19 and
                                how you can help reduce the spread of the virus in your community, tap here.</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr><td >&nbsp;</td></tr>';
    $pdfcontent .= '</table>';
    $pdfcontent .= '<table  class="block" border="0" style="width: 100%;margin-bottom: 50px; font-size: 12px;border-top: 0.1px solid #333333">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td rowspan="3"  class="barcodecell">';
    $pdfcontent .= '<barcode code="' . $reportInfo['qrcode_file_url'] . '" type="QR" class="barcode" size="1" error="M" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
    $pdfcontent .= '<img src="' . __DIR__ . '/images/sign.jpg" style="width: 90px;"/>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    //$pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
    $pdfcontent .= $test_location;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'Physician Oversight: Dr. Dominique M Thompson #ME134892';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';
}

$pdfcontent .= '</body>';
$mpdf->WriteHTML($pdfcontent);
// $mpdf->Output();exit;
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
        $q = mysqli_query($con, "UPDATE tbl_report SET pdf_file_url='" . $result["ObjectURL"] . "',pdf_file_name='" . $pdf_file_name . "' WHERE report_id=$id");
        unlink($output);
    }
} catch (S3Exception $e) {
    echo ($e->getMessage());
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
