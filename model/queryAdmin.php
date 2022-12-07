<?php

use Mpdf\Mpdf;
use Postmark\PostmarkClient;

session_start();
date_default_timezone_set('America/Los_Angeles');

include('../includes/functions.php');
// include("../global/variables.php");

if (isset($_POST['formAction'])) {
    $formAction = p($_POST['formAction']);

    switch ($formAction) {
        case 'saveEmployee':
            saveEmployee();
            exit;
        // case 'addCustomer':
        //     addCustomer();
        //     exit;
        case 'addEmployee':
            addEmployee();
            exit;
        default:
            echo '<script> alert("No form action"); </script>';
            die;
    }
}

$method = '';
if (isset($_POST['method'])) {
    $method = p($_POST['method']);
}
switch ($method) {
    case 'addReport':
        addReport();
        break;
    case 'deleteRow':
        deleteRow();
        break;
    case 'deleteReport':
        deleteReport();
        break;
    case 'sendEmail':
        sendEmail();
        break;
    case 'downloadPDF':
        downloadPDF();
        break;
    case 'searchClient':
        searchClient();
        break;
    case 'getClientAppointmentInfo':
        getClientAppointmentInfo();
        break;
    default:
        echo 'alert("You are now being Tracked");';
        die;
}

function addEmployee()
{
    //if(!adminLoggedIn()) { echo 'no admin'; return; }
    $con = dbCon();
    $name = $_POST['add_name'];
    $pass = md5($_POST['add_password']);
    $email = $_POST['add_email'];
    $role = $_POST['add_role'];
    $role = 1;

    $date_created = date('Y-m-d H:i:s');
    $date_modifed = date('Y-m-d H:i:s');

    $q = mysqli_query($con, "INSERT INTO tbl_users (`id`, `name`, `email`, `role`, `password`, `date_created`, `date_modifed`) VALUES (NULL, '$name', '$email', '$role', '$pass', '$date_created', '$date_modifed')");
    if (!$q) {
        $result = ['failed'];
        echo json_encode($result);
        return;
    }


    $result = ['success'];
    echo json_encode($result);
    return;
}

function saveEmployee()
{
    //if(!adminLoggedIn()) { echo 'no admin'; return; }

    $con = dbCon();
    $ID = $_POST['ID'];
    $name = $_POST['name'];
    $pass = $_POST['password'];
    $email = $_POST['email'];

    $date_modifed = date("Y-m-d h:i:s");

    if ($pass == '') {
        $passQuery = "";
    } else {
        $pass = md5($_POST['password']);
        $passQuery = "password='$pass',";
    }

    $q = mysqli_query($con, "UPDATE tbl_users SET name='$name', " . $passQuery . " email='$email', date_modifed='$date_modifed' WHERE id=$ID");
    if (!$q || isset($workError)) {
        $op = ['failed'];
        echo json_encode($op);
        return;
    }


    $op = ['success'];
    echo json_encode($op);
    return;
}

function addReport()
{
    $con = dbCon();
    $id = $_POST['ID'];
    date_default_timezone_set('US/Eastern');
    $currenttime = date('Y-m-d h:i:s');
    $type_id = $_POST['type'];
    $location = $_POST['location'];
    $patient_firstname = $_POST['patient_firstname'];
    $patient_lastname = $_POST['patient_lastname'];
    $patient_phone = $_POST['patient_phone'];
    $patient_email = $_POST['patient_email'];
    $patient_birth = $_POST['patient_birth'];
    $patient_gender = $_POST['patient_gender'];
    $patient_passport = $_POST['patient_passport'];
    $report_results = $_POST['report_results'];
    $sample_taken = $_POST['sample_taken'];
    if ($type_id == 2)
        $patient_test_brand = $_POST['antigen_test_brand'];
    else if ($type_id == 5)
        $patient_test_brand = "OSOM ULTRA FLU A&B Screening";
    else
        $patient_test_brand = NULL;
    $user_id = $_SESSION['id'];
    $released = isset($_POST['released']) ? $_POST['released'] : "";
    $report_created_at = $currenttime;
    $report_updated_at = $currenttime;
    $handled_at = $currenttime;
    
    if ($id == 0) { //insert new one

        $maxTokenQ = "SELECT user_token FROM `tbl_report` ORDER BY report_id DESC LIMIT 0,1";
        $maxResult = mysqli_query($con, $maxTokenQ);
        $token = mysqli_fetch_row($maxResult);
        $userToken = (int)$token[0];
        $newUserToken = str_pad($userToken + 1, 16, '0', STR_PAD_LEFT);

        $q = mysqli_query($con, "INSERT INTO tbl_report (`report_id`, `user_token`,`type_id`, `location`, `patient_firstname`, `patient_lastname`, `patient_phone`, `patient_email`, `patient_birth`, `patient_gender`, `patient_passport`, `report_results`, `sample_taken`,`user_id` , `released`, `report_created_at`, `handled_at`, `antigen_test_brand`) VALUES (NULL, '$newUserToken', '$type_id', '$location', '$patient_firstname', '$patient_lastname', '$patient_phone', '$patient_email', '$patient_birth', '$patient_gender', '$patient_passport', '$report_results', '$sample_taken', '$user_id', '$released', '$report_created_at', '$handled_at', '$patient_test_brand')");
        $id = mysqli_insert_id($con);
        if ($q) {
            $result = false;
            echo json_encode(array('id' => $id, 'result' => $q));
            return;
        }
    } else { //update
        // var_dump("UPDATE tbl_report SET type_id='$type_id', patient_firstname='$patient_firstname', patient_lastname='$patient_lastname', patient_phone='$patient_phone', patient_email='$patient_email', patient_birth='$patient_birth', patient_gender='$patient_gender', patient_passport='$patient_passport', report_results='$report_results', sample_taken='$sample_taken', released='$released', handled_at='$handled_at', report_updated_at='$report_updated_at' WHERE id=$id");die;
        $q = mysqli_query($con, "UPDATE tbl_report SET type_id='$type_id', location='$location', patient_firstname='$patient_firstname', patient_lastname='$patient_lastname', patient_phone='$patient_phone', patient_email='$patient_email', patient_birth='$patient_birth', patient_gender='$patient_gender', patient_passport='$patient_passport', report_results='$report_results', sample_taken='$sample_taken', released='$released', handled_at='$handled_at', report_updated_at='$report_updated_at', antigen_test_brand='$patient_test_brand' WHERE report_id=$id");
    }
    if ($id > 0) {
        //        Create & Upload files on S3
        uploadFilesOnAws($id);
    }
    echo json_encode(array('id' => $id, 'result' => $q));
    return;
}

function deleteReport()
{
    $con = dbCon();
    $ID = (int)p($_POST['id']);

    $q = mysqli_query($con, "DELETE FROM tbl_report WHERE report_id = $ID");
    if (!$q) {
        $result = ['failed'];
        echo json_encode($result);
        return;
    }

    $result = ['success'];
    echo json_encode($result);
    return;
}


function getClientAppointmentInfo()
{
    $con = dbCon();
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    $query = "SELECT * FROM tbl_appointment where acuity_appointment_id=$id";
    $result = mysqli_query($con, $query);
    $clientInfo = mysqli_fetch_assoc($result);
    $clientInfo['sample_collected_date'] = (!empty($clientInfo['sample_collected_date'])) ? date('D, d F Y h:i:s A', strtotime($clientInfo['sample_collected_date'])) : null;
    echo json_encode($clientInfo);
}

function searchClient()
{
    error_reporting(0);
    $con = dbCon();
    $query = '';
    if (isset($_POST['searchTerm'])) {
        $searchTerm = ucwords($_POST['searchTerm']);
        $query = "WHERE BINARY name like '%$searchTerm%'";
    }
    $query = "SELECT acuity_appointment_id as id,concat(firstName,' ',lastName,' ----',dob) as text FROM tbl_appointment $query  ORDER BY id DESC limit 40";
    $result = mysqli_query($con, $query);
    $types = array();
    while (($row = mysqli_fetch_assoc($result))) {
        $types[] = $row;
    }
    echo json_encode($types);
}

function downloadPDF()
{
    
    $con = dbCon();
    $id = (int)p($_POST['id']);
    $type_id = 0;

    $query = "SELECT * FROM tbl_report WHERE report_id=$id";
    $qo = mysqli_query($con, $query);
    if (mysqli_num_rows($qo) > 0) {
        $fo = mysqli_fetch_array_n($qo, MYSQLI_ASSOC);

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
    
    if ($type_id == 1)
        $output = "../uploads/8625a3599232039a77533b31ba47469cpdf/66c0300281470a91a1062263462f82e9pcr" . $id . ".pdf";
    if ($type_id == 2)
        $output = "../uploads/8625a3599232039a77533b31ba47469cpdf/b8af05afc5dbf0a9cbe3c02ab2962f0aantigen" . $id . ".pdf";
    if ($type_id == 3)
        $output = "../uploads/8625a3599232039a77533b31ba47469cpdf/c742a402367f2030636af25ce0c9713eaccula" . $id . ".pdf";
    if ($type_id == 4)
        $output = "../uploads/8625a3599232039a77533b31ba47469cpdf/8513da060dea559ed3dff467ecb2651eantibody" . $id . ".pdf";
    if ($type_id == 5)
        $output = "../uploads/8625a3599232039a77533b31ba47469cpdf/8513da060dea559ed3dff467ecb2651eaflu" . $id . ".pdf";
    $gender = ($patient_gender == 0) ? "Male" : "Female";
    $result = ($report_results == 0) ? "Negative" : "Positive";
    $result_img = ($report_results == 0) ? "../assets/images/negative.png" : "../assets/images/positive.png";
    $antibody_result = ($report_results == 0) ? "NOT DETECTED" : "DETECTED";

    $updated_at = date('F d Y', strtotime($report_updated_at));

    // Include mpdf library file
    require_once '../vendor/autoload.php';
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
    .results { width: 120px; }
    .barcode { padding: 1.5mm; margin: 0; vertical-align: top; color: #000000;}
    .barcodecell { text-align: center; vertical-align: middle; padding: 0;}
    .logo { width: 170px; margin-left: 40px;}
    .block { margin-right: 40px; margin-left: 40px;}
    .header { height:350px; width: 100%; vertical-align: bottom; background-color:#dad7e3; }
    .main_text { font-size: 18px;}
    .second_text { /* color: #57697e; */ } </style><body>';

    $mpdf->SetFont("Roboto-Regular");
    
    
    if ($type_id == 1) {
        // qr code part;
        $url = '../QRcode/Visby' . $id . '.pdf';
        $generatedQRcodeReport = generate_qrcode_report($type_id, $url, $fo, $id);
        if($generatedQRcodeReport === true) {
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
            <td width="37%;"><input type="image" class="logo" src="../images/fast.png" /></td>
            
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
            $pdfcontent .= '<img src="../images/sign.jpg" style="width: 90px;"/>';
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
    }
    if ($type_id == 2) {
        $url = '../QRcode/Antigen' . $id . '.pdf';
        
        $generatedQRcodeReport = generate_qrcode_report($type_id, $url, $fo, $id);
        if($generatedQRcodeReport === true) {
            $queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
            if (mysqli_num_rows($queryObj) > 0) {
                $reportInfo = mysqli_fetch_assoc($queryObj);
            }
            $pdfcontent .= '<table width="100%" class="header">
                <tr><td >&nbsp;</td></tr>
                <tr><td >&nbsp;</td></tr>
                <tr>
                <td width="28%;"><input type="image" class="logo" src="../images/fast.png" /></td>
                
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
            $pdfcontent .= '<img src="../images/sign.jpg" style="width: 90px;"/>';
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
    }
    if ($type_id == 3) {
        $url = '../QRcode/Accula' . $id . '.pdf';
        $generatedQRcodeReport = generate_qrcode_report($type_id, $url, $fo, $id);
        if($generatedQRcodeReport === true) {
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
            <td width="37%;"><input type="image" class="logo" src="../images/fast.png" /></td>
            
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
            $pdfcontent .= '<img src="../images/sign.jpg" style="width: 90px;"/>';
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
    }
    if ($type_id == 4) {
        $url = '../QRcode/Antibody' . $id . '.pdf';
        $generatedQRcodeReport = generate_qrcode_report($type_id, $url, $fo, $id);
        if($generatedQRcodeReport === true) {
            $queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
            if (mysqli_num_rows($queryObj) > 0) {
                $reportInfo = mysqli_fetch_assoc($queryObj);
            }
            // var_dump($updated_at);die;
            $pdfcontent .= '<table width="100%" class="header">
                <tr><td >&nbsp;</td></tr>
                <tr><td >&nbsp;</td></tr>
                <tr>
                <td width="28%;"><input type="image" class="logo" src="../images/fast.png" /></td>
                
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
            $pdfcontent .= '<img src="../images/sign.jpg" style="width: 90px;"/>';
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
    }
    if ($type_id == 5) {
        $url = '../QRcode/Flu' . $id . '.pdf';
        $generatedQRcodeReport = generate_qrcode_report($type_id, $url, $fo, $id);
        if($generatedQRcodeReport === true) {
            $queryObj = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
            if (mysqli_num_rows($queryObj) > 0) {
                $reportInfo = mysqli_fetch_assoc($queryObj);
            }
            // var_dump($updated_at);die;
            $pdfcontent .= '<table width="100%" class="header">
                <tr><td >&nbsp;</td></tr>
                <tr><td >&nbsp;</td></tr>
                <tr>
                <td width="28%;"><input type="image" class="logo" src="../images/fast.png" /></td>
                
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
            $pdfcontent .= "Method: <b>: OSOM ULTRA FLU A&B Screening</b> <br>";
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
            $pdfcontent .= '<img src="../images/sign.jpg" style="width: 90px;"/>';
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
    }
    $pdfcontent .= '</body>';

    // echo $pdfcontent; exit;

    $mpdf->WriteHTML($pdfcontent);

    //output in browser
    $mpdf->Output($output, 'F');

    $result = true;
    // echo json_encode(array('id'=>$id, 'result'=>$result));
    sendEmail($_POST['action'], $id, $reportInfo['user_token']);
    return;
}

function sendEmail($action, $id, $test_id)
{
    $con = dbCon();
    include "../libraries/postmark.php";

    //getting sender email address
    if (isset($_SESSION['id']))
        $admin_id = $_SESSION['id'];
    $q = mysqli_query($con, "SELECT * FROM tbl_users WHERE id=$admin_id");
    $f = mysqli_fetch_array_n($q, MYSQLI_ASSOC);
    $sender = $f['email'];
    $sender = "results@fasttestnow.health";

    //getting receiver email address
    $qr = mysqli_query($con, "SELECT * FROM tbl_report WHERE report_id=$id");
    $fr = mysqli_fetch_array_n($qr, MYSQLI_ASSOC);
    $receiver = $fr['patient_email'];
    $type_id = $fr['type_id'];
    $pdf_name = '';
    $awsPdfFile = null;
    if ($type_id == 1) {
        $output = "../uploads/8625a3599232039a77533b31ba47469cpdf/66c0300281470a91a1062263462f82e9pcr" . $id . ".pdf";
        $awsPdfFile = "66c0300281470a91a1062263462f82e9pcr" . $id . ".pdf";
        $pdf_name = "Rt-PCR";
        $type = "Visby";
        $subject_line = "Results RT-PCR Test [" . $fr['patient_firstname'] . " " . $fr['patient_lastname'] . "]";
    }
    if ($type_id == 2) {
        $output = "../uploads/8625a3599232039a77533b31ba47469cpdf/b8af05afc5dbf0a9cbe3c02ab2962f0aantigen" . $id . ".pdf";
        $awsPdfFile = "b8af05afc5dbf0a9cbe3c02ab2962f0aantigen" . $id . ".pdf";
        $pdf_name = "Antigen";
        $type = "Antigen";
        $subject_line = 'Hello ' . $fr['patient_firstname'];
    }
    if ($type_id == 3) {
        $output = "../uploads/8625a3599232039a77533b31ba47469cpdf/c742a402367f2030636af25ce0c9713eaccula" . $id . ".pdf";
        $awsPdfFile = "c742a402367f2030636af25ce0c9713eaccula" . $id . ".pdf";
        $pdf_name = "Accula";
        $type = "Accula";
        $subject_line = "Results RT-PCR Test [" . $fr['patient_firstname'] . " " . $fr['patient_lastname'] . "]";
    }
    if ($type_id == 4) {
        $output = "../uploads/8625a3599232039a77533b31ba47469cpdf/8513da060dea559ed3dff467ecb2651eantibody" . $id . ".pdf";
        $awsPdfFile = "8513da060dea559ed3dff467ecb2651eantibody" . $id . ".pdf";
        $pdf_name = "Antibody";
        $type = "Antibody";
        $subject_line = 'Hello ' . $fr['patient_firstname'];
    }
    if ($type_id == 5) {
        $output = "../uploads/8625a3599232039a77533b31ba47469cpdf/8513da060dea559ed3dff467ecb2651eaflu" . $id . ".pdf";
        $awsPdfFile = "8513da060dea559ed3dff467ecb2651eaflu" . $id . ".pdf";
        $pdf_name = "Flu";
        $type = "Flu";
        $subject_line = 'Hello ' . $fr['patient_firstname'];
    }
    $subject = '';
    $message = '';
    if ($action == 1) {
        $subject = $subject_line;
        $message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
        <title>Fast test now</title>
        <style type="text/css">
        html { -webkit-text-size-adjust:none; -ms-text-size-adjust: none;}
        @media only screen and (max-device-width: 680px), only screen and (max-width: 680px) { 
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
                                <img src="../images/fast.png" width="135" alt="FastTestNow" border="0" style="display: block;" /></font></a>
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
                                                        Results ' . $pdf_name . ' Patient ID ' . $test_id . '
                                                    </span></font>
                                                <div style="line-height: 21px; padding: 0 5px;">
                                                    <font face="Arial, Helvetica, sans-serif" size="3" color="#2e2b2b" style="font-size: 15px;font-weight: bold;">
                                                    <table style="width: 100%; border-collapse: collapse;"><tr style="background-color: #ffffff;">
                                                        <td style="line-height: 2;padding-left: 5px;">' . (($type_id == 2 || $type_id == 4) ? 'Date & Time Test Taken' : 'Date & Time Released') . '</td>
                                                        <td style="line-height: 2;padding-left: 5px;">' . (($type_id == 2 || $type_id == 4) ? $fr['sample_taken'] : $fr['released']) . '</td>
                                                    </tr><tr style="background-color: #e7e9eb;">
                                                        <td style="line-height: 2;padding-left: 5px;">Patient Name</td>
                                                        <td style="line-height: 2;padding-left: 5px;">' . $fr['patient_firstname'] . ' ' . $fr['patient_lastname'] . '</td>
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
                                <a href="https://fasttestnowreports.s3.amazonaws.com/REPORTS/' . $awsPdfFile . '" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold;">
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
                                    Click User Portal Link	</font></a>
                                <!-- padding --><div style="height: 60px; line-height: 60px; font-size: 10px;">&nbsp;</div>
                            </td></tr>			
                            </tbody>
                        </table>
                        <div style="text-align: left; padding: 10px 25px;">
                            <font face="Arial, Helvetica, sans-serif" size="3" color="#717171" style="font-size: 27px;">
                            <span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold; color: #2a2929;">
                                ' . $type . ' Medical:
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
    } else {
        $subject = $subject_line;
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
                                <img src="../images/fast.png" width="135" alt="FastTestNow" border="0" style="display: block;" /></font></a>
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
                                                        Results ' . $pdf_name . ' Patient ID ' . $test_id . '
                                                    </span></font>
                                                <div style="line-height: 21px; padding: 0 5px;">
                                                    <font face="Arial, Helvetica, sans-serif" size="3" color="#2e2b2b" style="font-size: 15px;font-weight: bold;">
                                                    <table style="width: 100%; border-collapse: collapse;"><tr style="background-color: #ffffff;">
                                                        <td style="line-height: 2;padding-left: 5px;">' . (($type_id == 2 || $type_id == 4) ? 'Date & Time Test Taken' : 'Date & Time Released') . '</td>
                                                        <td style="line-height: 2;padding-left: 5px;">' . (($type_id == 2 || $type_id == 4) ? $fr['sample_taken'] : $fr['released']) . '</td>
                                                    </tr><tr style="background-color: #e7e9eb;">
                                                        <td style="line-height: 2;padding-left: 5px;">Patient Name</td>
                                                        <td style="line-height: 2;padding-left: 5px;">' . $fr['patient_firstname'] . ' ' . $fr['patient_lastname'] . '</td>
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
                                <a href="https://fasttestnowreports.s3.amazonaws.com/REPORTS/' . $awsPdfFile . '" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold;">
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
                                ' . $type . ' Medical:
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
    }
    // Send an email:
    $postmark = new Postmark("d1082cae-330f-4dec-a6f7-75c2afb80081", "result@fasttestnow.health");
    $result = $postmark
        ->to($receiver)
        ->subject($subject)
        ->plain_message("You've received an encrypted message from FastTestNow&#174;")
        ->html_message($message)
        ->attachment('results.pdf', base64_encode(file_get_contents($output)), 'application/pdf')
        ->send();
    //    Delete PDF from Server Folder
    unlink($output);
    echo json_encode($result);
    return;
}

function uploadFilesOnAws($reportID)
{
    // create a new cURL resource
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://fasttestnow.health/model/downloadPDF.php?id=" . $reportID);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
}
