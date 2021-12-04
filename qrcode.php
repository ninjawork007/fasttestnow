<?php
require_once __DIR__ . '/vendor/autoload.php';

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Mpdf\Mpdf;


function generate_qrcode_report($type_id, $source_url, $data, $reportID)
{
    //Delete AWS file
    if (!empty($source_url)) {
        try {
            //Initial S3 Client
            $client = new S3Client([
                'credentials' => [
                    'key' => 'AKIAW7ZPM2DM4VGHHSFN',
                    'secret' => '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'
                ],
                'region' => 'us-east-1',
                'version' => 'latest',
            ]);
            //    Delete File
            $result = $client->deleteObject(array(
                'Bucket' => 'fasttestnowreports',
                'Key' => $source_url,
            ));
        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";exit;
        }
    }
    switch ($type_id) {
        case 1:
            generate_visby_result($source_url, $data, $reportID);
            break;
        case 2:
            generate_antigen_result($source_url, $data, $reportID);
            break;
        case 3:
            generate_accula_result($source_url, $data, $reportID);
            break;
        case 4:
            generate_antibody_result($source_url, $data, $reportID);
            break;
        case 5:
            generate_flu_result($source_url, $data, $reportID);
            break;    
        default:
            echo "generating pdf error";
            die;
    }
}

function generate_visby_result($url, $data, $reportID)
{
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'margin_left' => 20,
        'margin_right' => 20,
        'margin_top' => 0,
        'margin_bottom' => 10,
        'margin_header' => 5,
        'margin_footer' => 10,
        'format' => 'A4',
        'default_font' => 'Roboto-Regular'
    ]);
    $mpdf->SetWatermarkText("QR Code");
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.1;
    $type_id = $data['type_id'];
    $sample_taken = $data['sample_taken'];
    $patient_firstname = $data['patient_firstname'];
    $patient_lastname = $data['patient_lastname'];
    $patient_birth = $data['patient_birth'];
    $patient_email = $data['patient_email'];
    $patient_gender = $data['patient_gender'];
    $patient_phone = $data['patient_phone'];
    $patient_passport = $data['patient_passport'];
    $report_results = $data['report_results'];
    $report_updated_at = $data['report_updated_at'];
    $released = $data['released'];

    $gender = ($patient_gender == 0) ? "Male" : "Female";
    $result = ($report_results == 0) ? "Negative" : "Positive";
    $result_img = ($report_results == 0) ? "assets/images/negative.png" : "assets/images/positive.png";
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
                    }
                    .main_text {
                        font-size: 18px;
                    }
                </style>

            <body>';
    $pdfcontent .= '<table width="100%"  style="height:350px;border-top: 0.1mm solid #ccc; vertical-align: bottom;">
                    <tr><td width="33%">&nbsp;</td></tr>
                    <tr><td width="33%">&nbsp;</td></tr>
                    <tr>
                        <td width="33%"></td>
                        <td style="text-align: center;vertical-align:middle;">Pateint ID: ' . $data['user_token'] . '</td>
                    </tr>
                    <tr>
                    <td width="37%;"><img src="' . __DIR__ . '/images/logo.png" style="width: 170px;float:right;"/></td>
                    
                    <td style="text-align: left;vertical-align:middle;">
                        <h2>Sars-Cov-2 Rt-PCR Screening Results</h2>
                    </td>
                    </tr>
                    <tr><td width="33%">&nbsp;</td></tr>
                    </table>';

    $pdfcontent .= '<table class="main_text" border="0" style="width: 100%;">';
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
    $pdfcontent .= '<td style="width: 10%; height:50px">';
    $pdfcontent .= '<b>Gender</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align:left;color:#6a7180">';
    $pdfcontent .= $gender;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Email</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180;text-align:left">';
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

    $pdfcontent .= '<table border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td></td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= '<b>Sample Type:</b> Nasopharyngeal Swab&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b> Test manufacturer/kit:</b> bioteke corporation';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td colspan="2">';
    $pdfcontent .= "<b>Sample processed by:</b><b> Pinnacle Genetics Labs Inc</b> using rt-pcr test kit: <u style='color:blue;'>Factsheet for patients</u><br>";
    $pdfcontent .= "The United States FDA has made this test available under an emergency access mechanism called an Emergency Use Authorization";
    $pdfcontent .= "(EUA). The EUA is supported by the Secretary of Health and Human Service's (HHS's) declaration that circumstances exist to justify the";
    $pdfcontent .= "emergency use of in vitro diagnostics (IVDs) for the detection and/or diagnosis of the virus that causes COVID-19. EUA# EUA203089";
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

    $pdfcontent .= '<table border="0" style="width: 100%;padding-left: 20px;padding-right: 20px;">';
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
    $pdfcontent .= '<input type="image" class="logo" src="' . __DIR__ . '/images/hipaa-compliance.png" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
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
    $mpdf->Output($url, 'F');
    //Upload File
    try {
        //Initial S3 Client
        $client = new S3Client([
            'credentials' => [
                'key' => 'AKIAW7ZPM2DM4VGHHSFN',
                'secret' => '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'
            ],
            'region' => 'us-east-1',
            'version' => 'latest',
        ]);
        //    Upload File
        $result = $client->putObject([
            'Bucket' => 'fasttestnowreports',
            'Key' => $url,
            'SourceFile' => $url
        ]);
        if ($result["@metadata"]["statusCode"] == '200') {
            $pdf_file_name = basename($result["ObjectURL"]);
            $q = mysqli_query(dbCon(), "UPDATE tbl_report SET qrcode_file_url='" . $result["ObjectURL"] . "',qrcode_file_name='" . $pdf_file_name . "' WHERE report_id=$reportID");
            unlink($url);
        }
    } catch (S3Exception $e) {
        echo '<pre/>';
        print_r($e->getMessage());
        exit;
    }
    return $url;
}

function generate_antigen_result($url, $data, $reportID)
{
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'margin_left' => 20,
        'margin_right' => 20,
        'margin_top' => 0,
        'margin_bottom' => 10,
        'margin_header' => 5,
        'margin_footer' => 10,
        'format' => 'A4',
        'default_font' => 'Roboto-Regular'
    ]);
    $mpdf->SetWatermarkText("QR Code");
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.1;
    $type_id = $data['type_id'];
    $sample_taken = $data['sample_taken'];
    $patient_firstname = $data['patient_firstname'];
    $patient_lastname = $data['patient_lastname'];
    $patient_birth = $data['patient_birth'];
    $patient_email = $data['patient_email'];
    $patient_gender = $data['patient_gender'];
    $patient_phone = $data['patient_phone'];
    $patient_passport = $data['patient_passport'];
    $report_results = $data['report_results'];
    $report_updated_at = $data['report_updated_at'];
    $released = $data['released'];
    $gender = ($patient_gender == 0) ? "Male" : "Female";
    $result = ($report_results == 0) ? "Negative" : "Positive";
    $result_img = ($report_results == 0) ? "assets/images/negative.png" : "assets/images/positive.png";
    
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
                    }
                    .main_text {
                        font-size: 18px;
                    }
                </style>

            <body>';
    $pdfcontent .= '<table width="100%"  style="height:350px;border-top: 0.1mm solid #ccc; vertical-align: bottom;">
            <tr><td >&nbsp;</td></tr>
            <tr><td >&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr style="height:250px">
            <td width="28%;"><img src="' . __DIR__ . '/images/logo.png" style="width: 170px;float:right;"/></td>
            
            <td width="40%;" style="text-align: center;vertical-align:middle;">
                <h2>Rapid Antigen Results</h2>
            </td>
            <td style="text-align: right;vertical-align:middle;">
                <span style="font-size:12px;">Patient ID: &nbsp;&nbsp;&nbsp;&nbsp;' . $data['user_token'] . '</span> <br/><br/><br/>
                <h3>' . date("D, M d, Y") . '</h3>
            </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
        </table>';
    $pdfcontent .= '<table class="main_text" border="0" style="width: 100%;">';
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
    $pdfcontent .= '<td style="width: 10%;height:50px">';
    $pdfcontent .= '<b>Gender</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180">';
    $pdfcontent .= $gender;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Passport</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%">';
    $pdfcontent .= $patient_passport;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Results</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180">';
    $pdfcontent .= '<input type="image" class="results" src="' . $result_img . '" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table border="0" style="width: 100%;padding-left: 20px;padding-right: 20px;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;">';
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
    $pdfcontent .= "<b>Abbot BinaxNOW</b> Covid-19 Antigen Test: <br>";
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

    $pdfcontent .= '<table border="0" style="width: 100%;padding-left: 20px;padding-right: 20px;">';
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
    $pdfcontent .= '<input type="image" class="logo" src="' . __DIR__ . '/images/hipaa-compliance.png" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'Physician Oversight: Dr. Dominique M Thompson #ME134892';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';
    $mpdf->WriteHTML($pdfcontent);
    //output in browser
    $mpdf->Output($url, 'F');
    try {
        //Initial S3 Client
        $client = new S3Client([
            'credentials' => [
                'key' => 'AKIAW7ZPM2DM4VGHHSFN',
                'secret' => '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'
            ],
            'region' => 'us-east-1',
            'version' => 'latest',
        ]);
        //    Upload File
        $result = $client->putObject([
            'Bucket' => 'fasttestnowreports',
            'Key' => $url,
            'SourceFile' => $url
        ]);
        if ($result["@metadata"]["statusCode"] == '200') {
            $pdf_file_name = basename($result["ObjectURL"]);
            $q = mysqli_query(dbCon(), "UPDATE tbl_report SET qrcode_file_url='" . $result["ObjectURL"] . "',qrcode_file_name='" . $pdf_file_name . "' WHERE report_id=$reportID");
            unlink($url);
        }
    } catch (S3Exception $e) {
        echo '<pre/>';
        print_r($e->getMessage());
        exit;
    }
    return $url;
}

function generate_accula_result($url, $data, $reportID)
{
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'margin_left' => 20,
        'margin_right' => 20,
        'margin_top' => 0,
        'margin_bottom' => 10,
        'margin_header' => 5,
        'margin_footer' => 10,
        'format' => 'A4',
        'default_font' => 'Roboto-Regular'
    ]);
    $mpdf->SetWatermarkText("QR Code");
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.1;
    $type_id = $data['type_id'];
    $sample_taken = $data['sample_taken'];
    $patient_firstname = $data['patient_firstname'];
    $patient_lastname = $data['patient_lastname'];
    $patient_birth = $data['patient_birth'];
    $patient_email = $data['patient_email'];
    $patient_gender = $data['patient_gender'];
    $patient_phone = $data['patient_phone'];
    $patient_passport = $data['patient_passport'];
    $report_results = $data['report_results'];
    $report_updated_at = $data['report_updated_at'];
    $released = $data['released'];
    $gender = ($patient_gender == 0) ? "Male" : "Female";
    $result = ($report_results == 0) ? "Negative" : "Positive";
    $result_img = ($report_results == 0) ? "assets/images/negative.png" : "assets/images/positive.png";
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
                    }
                    .main_text {
                        font-size: 18px;
                    }
                </style>

            <body>';
    $pdfcontent .= '<table width="100%"  style="height:350px;border-top: 0.1mm solid #ccc; vertical-align: bottom;">
                        <tr><td width="33%">&nbsp;</td></tr>
                        <tr><td width="33%">&nbsp;</td></tr>
                        <tr>
                            <td width="33%"></td>
                            <td style="text-align: right;vertical-align:middle;">Pateint ID: ' . $data['user_token']. '</td>
                        </tr>
                        <tr>
                        <td width="37%;"><img src="' . __DIR__ . '/images/logo.png" style="width: 170px;float:right;"/></td>
                        
                        <td style="text-align: left;vertical-align:middle;">
                            <h2>Accula Rt-PCR Screening Results</h2>
                        </td>
                        </tr>
                        <tr><td width="33%">&nbsp;</td></tr>
                </table>';
    $pdfcontent .= '<table class="main_text" border="0" style="width: 100%;">';
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
    $pdfcontent .= '<td style="width: 10%; height:50px">';
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

    $pdfcontent .= '<table border="0" style="width: 100%;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;">';
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
    $pdfcontent .= "The United States FDA has made this test available under an emergency access mechanism called an Emergency Use Authorization";
    $pdfcontent .= "(EUA). The EUA is supported by the Secretary of Health and Human Service's (HHS's) declaration that circumstances exist to justify the";
    $pdfcontent .= "emergency use of in vitro diagnostics (IVDs) for the detection and/or diagnosis of the virus that causes COVID-19. EUA# EUA203089";
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

    $pdfcontent .= '<table border="0" style="width: 100%;padding-left: 20px;padding-right: 20px;">';
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
    $pdfcontent .= '<input type="image" class="logo" src="' . __DIR__ . '/images/hipaa-compliance.png" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'Physician Oversight: Dr. Dominique M Thompson #ME134892';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';
    $mpdf->WriteHTML($pdfcontent);
    //output in browser
    $mpdf->Output($url, 'F');
    try {
        //Initial S3 Client
        $client = new S3Client([
            'credentials' => [
                'key' => 'AKIAW7ZPM2DM4VGHHSFN',
                'secret' => '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'
            ],
            'region' => 'us-east-1',
            'version' => 'latest',
        ]);
        //    Upload File
        $result = $client->putObject([
            'Bucket' => 'fasttestnowreports',
            'Key' => $url,
            'SourceFile' => $url
        ]);
        if ($result["@metadata"]["statusCode"] == '200') {
            $pdf_file_name = basename($result["ObjectURL"]);
            $q = mysqli_query(dbCon(), "UPDATE tbl_report SET qrcode_file_url='" . $result["ObjectURL"] . "',qrcode_file_name='" . $pdf_file_name . "' WHERE report_id=$reportID");
            unlink($url);
        }
    } catch (S3Exception $e) {
        echo '<pre/>';
        print_r($e->getMessage());
        exit;
    }
    return $url;
}

function generate_antibody_result($url, $data, $reportID)
{
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'margin_left' => 20,
        'margin_right' => 20,
        'margin_top' => 0,
        'margin_bottom' => 10,
        'margin_header' => 5,
        'margin_footer' => 10,
        'format' => 'A4',
        'default_font' => 'Roboto-Regular'
    ]);
    $mpdf->SetWatermarkText("QR Code");
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.1;
    $type_id = $data['type_id'];
    $sample_taken = $data['sample_taken'];
    $patient_firstname = $data['patient_firstname'];
    $patient_lastname = $data['patient_lastname'];
    $patient_birth = $data['patient_birth'];
    $patient_email = $data['patient_email'];
    $patient_gender = $data['patient_gender'];
    $patient_phone = $data['patient_phone'];
    $patient_passport = $data['patient_passport'];
    $report_results = $data['report_results'];
    $report_updated_at = $data['report_updated_at'];
    $released = $data['released'];
    $gender = ($patient_gender == 0) ? "Male" : "Female";
    $result = ($report_results == 0) ? "Negative" : "Positive";
    $result_img = ($report_results == 0) ? "assets/images/negative.png" : "assets/images/positive.png";
    $antibody_result = ($report_results == 0) ? "NOT DETECTED" : "DETECTED";
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
                    }
                    .main_text {
                        font-size: 18px;
                    }
                </style>

            <body>';
    $pdfcontent .= '<table width="100%"  style="height:350px;border-top: 0.1mm solid #ccc; vertical-align: bottom;">
                        <tr><td >&nbsp;</td></tr>
                        <tr><td >&nbsp;</td></tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr style="height:250px">
                        <td width="28%;"><img src="' . __DIR__ . '/images/logo.png" style="width: 170px;float:right;"/></td>
                        
                        <td width="40%;" style="text-align: center;vertical-align:middle;">
                            <h2>Rapid Antibody Screening Results</h2>
                        </td>
                        <td style="text-align: right;vertical-align:middle;">
                            <span style="font-size:12px;">Patient ID: &nbsp;&nbsp;&nbsp;&nbsp;' . $data['user_token'] . '</span> <br/><br/><br/>
                            <h3>' . date("D, M d, Y") . '</h3>
                        </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr><td>&nbsp;</td></tr>
                    </table>';
    $pdfcontent .= '<table class="main_text" border="0" style="width: 100%;">';
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
    $pdfcontent .= '<td style="width: 10%; height:50px;">';
    $pdfcontent .= '<b>Gender</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180">';
    $pdfcontent .= $gender;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Passport</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%">';
    //$pdfcontent .= $patient_passport;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Results</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180">';
    $pdfcontent .= $antibody_result;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table border="0" style="width: 100%;padding-left: 20px;padding-right: 20px;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;">';
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
    $pdfcontent .= "<b>Abbot BinaxNOW</b> Covid-19 Antibody Test: <br>";
    $pdfcontent .= "<u style='color:blue;'>FDA | Factsheet for patients</u>";
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

    $pdfcontent .= '<table border="0" style="width: 100%;padding-left: 20px;padding-right: 20px;">';
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
    $pdfcontent .= '<input type="image" class="logo" src="' . __DIR__ . '/images/hipaa-compliance.png" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'Physician Oversight: Dr. Dominique M Thompson #ME134892';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';
    $mpdf->WriteHTML($pdfcontent);
    //output in browser
    $mpdf->Output($url, 'F');
    try {
        //Initial S3 Client
        $client = new S3Client([
            'credentials' => [
                'key' => 'AKIAW7ZPM2DM4VGHHSFN',
                'secret' => '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'
            ],
            'region' => 'us-east-1',
            'version' => 'latest',
        ]);
        //    Upload File
        $result = $client->putObject([
            'Bucket' => 'fasttestnowreports',
            'Key' => $url,
            'SourceFile' => $url
        ]);
        if ($result["@metadata"]["statusCode"] == '200') {
            $pdf_file_name = basename($result["ObjectURL"]);
            $q = mysqli_query(dbCon(), "UPDATE tbl_report SET qrcode_file_url='" . $result["ObjectURL"] . "',qrcode_file_name='" . $pdf_file_name . "' WHERE report_id=$reportID");
            unlink($url);
        }
    } catch (S3Exception $e) {
        echo '<pre/>';
        print_r($e->getMessage());
        exit;
    }
    return $url;
}
function generate_flu_result($url, $data, $reportID)
{
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'margin_left' => 20,
        'margin_right' => 20,
        'margin_top' => 0,
        'margin_bottom' => 10,
        'margin_header' => 5,
        'margin_footer' => 10,
        'format' => 'A4',
        'default_font' => 'Roboto-Regular'
    ]);
    $mpdf->SetWatermarkText("QR Code");
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.1;
    $type_id = $data['type_id'];
    $sample_taken = $data['sample_taken'];
    $patient_firstname = $data['patient_firstname'];
    $patient_lastname = $data['patient_lastname'];
    $patient_birth = $data['patient_birth'];
    $patient_email = $data['patient_email'];
    $patient_gender = $data['patient_gender'];
    $patient_phone = $data['patient_phone'];
    $patient_passport = $data['patient_passport'];
    $report_results = $data['report_results'];
    $report_updated_at = $data['report_updated_at'];
    $released = $data['released'];
    $gender = ($patient_gender == 0) ? "Male" : "Female";
    $result = ($report_results == 0) ? "Negative" : "Positive";
    $result_img = ($report_results == 0) ? "assets/images/negative.png" : "assets/images/positive.png";
    
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
                    }
                    .main_text {
                        font-size: 18px;
                    }
                </style>

            <body>';
    $pdfcontent .= '<table width="100%"  style="height:350px;border-top: 0.1mm solid #ccc; vertical-align: bottom;">
            <tr><td >&nbsp;</td></tr>
            <tr><td >&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr style="height:250px">
            <td width="28%;"><img src="' . __DIR__ . '/images/logo.png" style="width: 170px;float:right;"/></td>
            
            <td width="40%;" style="text-align: center;vertical-align:middle;">
                <h2>Rapid Antigen Results</h2>
            </td>
            <td style="text-align: right;vertical-align:middle;">
                <span style="font-size:12px;">Patient ID: &nbsp;&nbsp;&nbsp;&nbsp;' . $data['user_token'] . '</span> <br/><br/><br/>
                <h3>' . date("D, M d, Y") . '</h3>
            </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
        </table>';
    $pdfcontent .= '<table class="main_text" border="0" style="width: 100%;">';
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
    $pdfcontent .= '<td style="width: 10%;height:50px">';
    $pdfcontent .= '<b>Gender</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180">';
    $pdfcontent .= $gender;
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Passport</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%">';
    $pdfcontent .= $patient_passport;
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '<b>Results</b>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%;color:#6a7180">';
    $pdfcontent .= '<input type="image" class="results" src="' . $result_img . '" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 10%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="width: 40%">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';

    $pdfcontent .= '<table border="0" style="width: 100%;padding-left: 20px;padding-right: 20px;">';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="width: 50%;">';
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
    $pdfcontent .= "<b>Abbot BinaxNOW</b> Covid-19 Antigen Test: <br>";
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

    $pdfcontent .= '<table border="0" style="width: 100%;padding-left: 20px;padding-right: 20px;">';
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
    $pdfcontent .= '<input type="image" class="logo" src="' . __DIR__ . '/images/hipaa-compliance.png" />';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'https://fasttestnow.com - (833) 830 8383 - <u style="color:blue;">cs@fasttestnow.net</u>';
    $pdfcontent .= '</td>';
    $pdfcontent .= '<td rowspan="3" style="text-align: center;">';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= '2067 NE 163rd Street, North Miami Beach, FL 33162 | CLIA# 10D2214779';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '<tr>';
    $pdfcontent .= '<td style="text-align: right;">';
    $pdfcontent .= 'Physician Oversight: Dr. Dominique M Thompson #ME134892';
    $pdfcontent .= '</td>';
    $pdfcontent .= '</tr>';
    $pdfcontent .= '</table>';
    $mpdf->WriteHTML($pdfcontent);
    //output in browser
    $mpdf->Output($url, 'F');
    try {
        //Initial S3 Client
        $client = new S3Client([
            'credentials' => [
                'key' => 'AKIAW7ZPM2DM4VGHHSFN',
                'secret' => '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'
            ],
            'region' => 'us-east-1',
            'version' => 'latest',
        ]);
        //    Upload File
        $result = $client->putObject([
            'Bucket' => 'fasttestnowreports',
            'Key' => $url,
            'SourceFile' => $url
        ]);
        if ($result["@metadata"]["statusCode"] == '200') {
            $pdf_file_name = basename($result["ObjectURL"]);
            $q = mysqli_query(dbCon(), "UPDATE tbl_report SET qrcode_file_url='" . $result["ObjectURL"] . "',qrcode_file_name='" . $pdf_file_name . "' WHERE report_id=$reportID");
            unlink($url);
        }
    } catch (S3Exception $e) {
        echo '<pre/>';
        print_r($e->getMessage());
        exit;
    }
    return $url;
}

