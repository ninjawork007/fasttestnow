<?php

include('../OTP.php');
session_start();
require_once('../UserDetailClass.php');

if(!empty($_POST["method"])) {
	switch ($_POST['method']) {
		case 'getOTP':
			$email = $_POST['email'];
			$otpObj = new OTP($email, $otp = NULL);
			$otpObj->generateOTP();
			break;
		case 'validateOTP':
			$conn = dbCon();

			$submit_otp = $_POST['otp'];
			$otpObj = new OTP($email = NULL, $submit_otp);
			$flag = $otpObj->validateOTP();
			if(!$flag) {
				echo json_encode(array('notice'=>"Invalid OTP!"));
			} else {
				
				$users = [];
				$user = new UserDetail($fName = NULL, $lName = NULL, $_POST["email"]);
				$users = $user->getUserFromReport();
				
				$_SESSION['permission'] = 1;
				echo json_encode(array(
					"notice"=>"success",
					"data"=>json_encode($users)
				));	
			}

			break;
		default:
			# code...
			break;
	}
}