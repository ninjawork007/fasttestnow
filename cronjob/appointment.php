<?php
include('../includes/functions.php');
error_reporting(E_ALL);
date_default_timezone_set('US/Eastern');
$userID = '21660275';
$key = '99a1b7af6d84cffe3aa76f4b8dfff5b8';
// URL for all appointments
$url = 'https://acuityscheduling.com/api/v1/appointments?minDate=' . date('M d, Y') . '&maxDate=' . date('M d, Y') . '&direction=ASC&max=4000';

// Initiate curl:
// GET request, so no need to worry about setting post vars:
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, str_replace(' ', '%20', $url));

// Grab response as string:
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// HTTP auth:
curl_setopt($ch, CURLOPT_USERPWD, "$userID:$key");

// Execute request:
$result = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// Don't forget to close the connection!
curl_close($ch);
if ($httpcode == 200) {
    $appointments = json_decode($result, true);
    $con = dbCon();

    foreach ($appointments as $key => $value) {

        $formValues = $value['forms'][0]['values'];
        $id = $value['id'];
        $datetimeCreated = $value['datetimeCreated'];
        $sampleCollectedDate=date('Y-m-d H:i:s',strtotime($value['datetime']));
        $sampleCollectedDateFormatted=date('D, d M Y h:i:s A',strtotime($value['datetime']));
        $appointment_type = $value['type'];
        $firstName = $value['firstName'];
        $lastName = $value['lastName'];
        $phone = $value['phone'];
        $email = $value['email'];
        // Gender Column ID=>9030565
        $gender = $formValues[array_search('9030565', array_column($formValues, 'fieldID'))]['value'];
        // DOB Column ID=>9030567
        $dob = $formValues[array_search('9030567', array_column($formValues, 'fieldID'))]['value'];
        // Passport Country Column ID=>9030568
        $passportCountry = $formValues[array_search('9030568', array_column($formValues, 'fieldID'))]['value'];
        // Passport No Column ID=>9030569
        $passportNo = $formValues[array_search('9030569', array_column($formValues, 'fieldID'))]['value'];
        // Address Column ID=>9030570
        $address = $formValues[array_search('9030570', array_column($formValues, 'fieldID'))]['value'];
        // City Column ID=>9106592
        $city = $formValues[array_search('9106592', array_column($formValues, 'fieldID'))]['value'];
        // ZipCode Column ID=>9030579
        $zipCode = $formValues[array_search('9030579', array_column($formValues, 'fieldID'))]['value'];
        // Ethnicity Column ID=>9030580
        $ethnicity = $formValues[array_search('9030580', array_column($formValues, 'fieldID'))]['value'];
        // Symptoms Column ID=>9030603
        $symptoms = $formValues[array_search('9030603', array_column($formValues, 'fieldID'))]['value'];

        $checkExisting = mysqli_query($con, "select * from tbl_appointment where acuity_appointment_id=$id");
        $existingAppointment = mysqli_fetch_row($checkExisting);
        
        if (!empty($existingAppointment)) {
            $str = $firstName ." ". $lastName;
            $name1 = strtolower($str);
            $name = ucwords($name1);

            $q = mysqli_query($con, "update tbl_appointment set datetimeCreated='$datetimeCreated',sample_collected_date='$sampleCollectedDate',sample_collected_date_formatted='$sampleCollectedDateFormatted', name='$name', firstName='$firstName', lastName='$lastName', phone='$phone', email='$email', appointment_type='$appointment_type', gender='$gender', dob='$dob', passport_country='$passportCountry', passport_no='$passportNo', address='$address', city='$city', zipcode='$zipCode', ethnicity='$ethnicity', symptoms='$symptoms' where acuity_appointment_id = $id");
        } else {
            $str = $firstName ." ". $lastName;
            $name1 = strtolower($str);
            $name = ucwords($name1);
            
            $q = mysqli_query($con, "INSERT INTO tbl_appointment (`acuity_appointment_id`,`datetimeCreated`,`sample_collected_date`,`sample_collected_date_formatted`,`firstName`, `lastName`, `name`, `phone`, `email`, `appointment_type`, `gender`, `dob`, `passport_country`, `passport_no`, `address`,`city` , `zipcode`, `ethnicity`, `symptoms`) VALUES ('$id','$datetimeCreated','$sampleCollectedDate','$sampleCollectedDateFormatted', '$name','$firstName', '$lastName', '$phone', '$email', '$appointment_type', '$gender', '$dob', '$passportCountry', '$passportNo', '$address', '$city', '$zipCode', '$ethnicity','$symptoms')");
        }

    }
} else {
    print_r('Status Code=>' . $httpcode . '<br/>' . $result);
}




