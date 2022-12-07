<?php

require_once('../user_portal/OTP.php');
session_start();
require_once('../model/customerDetail.php');

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
				$user = new CustomerDetail($_POST["email"], $id = NULL);
				$users = $user->getUserTotalInfo();
				
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