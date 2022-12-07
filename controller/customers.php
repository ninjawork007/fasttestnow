<?php

require_once('../model/customerDetail.php');
require_once('../model/uploadReports.php');


$method = '';
if (isset($_POST['method'])) {
    $method = $_POST['method'];
}

switch ($method) {
    case 'getCustomerDetail':
        $id = $_POST['id'];
        $email = $_POST['email'];
        $user = new CustomerDetail($email, $id);
        
        echo json_encode($user->getUserTotalInfo());
        exit;
    case 'uploadCustomerReport':
        $data = array();
        $keys = array();

        $customerInfo = $_POST['userInfo'];
        $firstName = $customerInfo['firstName'];
        $lastName = $customerInfo['lastName'];
        $email = $customerInfo['email'];
        $gender = ($customerInfo['gender'] == 'Male')? 0: 1;
        $dob = $customerInfo['dob'];
        $pdfLink = $customerInfo['pdf_file_url'];
        $results = $_POST['results'];
        $type_id = $_POST['testType'];
        $appointment_id = $_POST['appointment_id'];

        array_push($keys, "appointment_id", "type_id", "firstname", "lastname", "email", "gender", "results", "dob", "pdfLink");
        array_push($data, $appointment_id, $type_id, $firstName, $lastName, $email, $gender, $results, $dob, $pdfLink);

        $UploadReports = new UploadReports($data);
        
        $result = $UploadReports->insert($keys);
        $id = $result['id'];
        $output = $_POST['filePath'];

        $res = $UploadReports->generatePDF($type_id, $output, $id);
        echo json_encode($res);
        break;
    default:
        echo '<script> alert("No form action"); </script>';
        die;
}