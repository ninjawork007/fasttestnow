<?php
include('../user_portal/includes/functions.php');

class OTP {
	private $email;
	private $otp;
	
	function __construct($email, $otp)
	{
		$this->email = $email;
		$this->otp = $otp;
	}
	private function createOTPTable() {
		$conn = dbCon();
		$sql = "CREATE TABLE IF NOT EXISTS `tbl_otp_expiry` (
			`id` int(11) NOT NULL,
			`otp` varchar(10) NOT NULL,
			`is_expired` int(11) NOT NULL,
			`member_id` int(11) NOT NULL,
			`email` varchar(250) NOT NULL,
			`create_at` datetime NOT NULL
		)";
		$result = mysqli_query($conn,$sql);
		if ($result === TRUE) {
			return true;
		  } else {
			return false;
		  }
	}
	public function generateOTP() {
		$conn = dbCon();
		$result = mysqli_query($conn,"SELECT * FROM tbl_report WHERE patient_email='" . $this->email . "' ORDER BY handled_at DESC");
		$familyMembers = array();
        while (($row = mysqli_fetch_assoc($result))) {
            $familyMembers[] = $row;
        }
		$member_id = $familyMembers[0]["report_id"];
		
		$count  = mysqli_num_rows($result);
		if($count > 0) {
			// generate OTP
			$otp = rand(100000,999999);
			// Send OTP
			require_once('../postmark.php');
			// include "postmark.php";
			$receiver = $this->email;
			$subject = "Fast Test Now Login Verification";
			$message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html>
			
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<title>Fast test now</title>
				<style type="text/css">
					html {
						-webkit-text-size-adjust: none;
						-ms-text-size-adjust: none;
					}
			
					@media only screen and (max-device-width: 680px),
					only screen and (max-width: 680px) {
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
						<span
							style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; color: #2a2929;">
							You\'ve received an encrypted message from FastTestNow&#174;
						</span>
					</font>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 320px;">
						<tr>
							<td align="center" bgcolor="#eff3f8">
			
			
								<!--[if gte mso 10]>
					<table width="680" border="0" cellspacing="0" cellpadding="0">
					<tr><td>
					<![endif]-->
			
								<table border="0" cellspacing="0" cellpadding="0" class="table_width_100" width="100%"
									style="max-width: 680px; min-width: 300px;">
									<tr>
										<td>
											<!-- padding -->
											<div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
										</td>
									</tr>
									<!--header -->
									<tr>
										<td align="center" bgcolor="#ffffff">
											<!-- padding -->
											<div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
											<table width="90%" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td align="center">
														<a href="https://fasttestnow.com/" target="_blank"
															style="color: #596167; font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
															<font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3"
																color="#596167">
																<img src="https://fasttestnow.health/images/fast.png" width="135"
																	alt="FastTestNow" border="0" style="display: block;" />
															</font>
														</a>
													</td>
												</tr>
											</table>
											<!-- padding -->
											<div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
										</td>
									</tr>
									<!--header END-->
			
									<!--goods -->
									<tr>
										<td align="center" bgcolor="#ffffff">
											<table width="90%" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td align="center">
														<table width="100%" border="0" cellspacing="0" cellpadding="0"
															style="line-height: 14px; padding: 0 25px;">
															<tbody>
																<tr>
																	<td style="width: 50%;">
																		<font face="Arial, Helvetica, sans-serif" size="5"
																			color="#57697e" style="font-size: 22px;">
																			<span
																				style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #2a2929; font-weight: bold;">
																				Verify Your Login
																			</span>
																		</font>
																		<!-- padding -->
																		<div
																			style="height: 60px; line-height: 60px; font-size: 10px;">
																			Below is your one time password.</div>
																	</td>
																</tr>
																<tr>
																	<td style="width: 50%;">
																		<font face="Arial, Helvetica, sans-serif" size="5"
																			color="#57697e" style="font-size: 22px;">
																			<span
																				style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #2a2929; font-weight: bold;">
																				'. $otp .'
																			</span>
																		</font>
																	</td>
																</tr>
															</tbody>
														</table>
			
													</td>
												</tr>
												<tr>
													<td>
														<!-- padding -->
														<div style="height: 10px; line-height: 10px; font-size: 10px;">&nbsp;</div>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<!--goods END-->
			
									<!--footer -->
									<tr>
										<td class="iage_footer" align="center" bgcolor="#fcfafb"
											style="border-top-width: 1px; border-top-style: solid; border-top-color: #ffffff;">
											<!-- padding -->
											<div style="height: 30px; line-height: 30px; font-size: 10px;">&nbsp;</div>
			
											<table width="100%" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td align="center">
														<font face="Arial, Helvetica, sans-serif" size="3" color="#717171"
															style="font-size: 13px;">
															<span
																style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #717171;">
																' . date('Y') . ' &copy; FastTestNow. ALL Rights Reserved.
															</span>
														</font>
													</td>
												</tr>
											</table>
			
											<!-- padding -->
											<div style="height: 30px; line-height: 30px; font-size: 10px;">&nbsp;</div>
										</td>
									</tr>
									<!--footer END-->
									<tr>
										<td>
											<!-- padding -->
											<div style="height: 40px; line-height: 40px; font-size: 10px;">&nbsp;</div>
										</td>
									</tr>
								</table>
								<!--[if gte mso 10]>
					</td></tr>
					</table>
					<![endif]-->
			
							</td>
						</tr>
					</table>
			
				</div>
			</body>
			
			</html>';
			
			$postmark = new Postmark("d1082cae-330f-4dec-a6f7-75c2afb80081", "result@fasttestnow.health");
			$mail_status = $postmark
				->to($receiver)
				->subject($subject)
				->plain_message("You've received an encrypted message from FastTestNow&#174;")
				->html_message($message)
				->send();
			
			if($mail_status === true) {
				
				$val = mysqli_query($conn,"SELECT * FROM tbl_otp_expiry LIMIT 1");
				if($val !== FALSE)
				{
					$result = mysqli_query($conn,"INSERT INTO tbl_otp_expiry(otp,is_expired,member_id, email,create_at) VALUES ('" . $otp . "', 0, '".$member_id."', '".$this->email."', '" . date("Y-m-d H:i:s"). "')");
					if($result === true) {
						
						echo json_encode(array('notice'=>"success", 'email'=>$this->email, 'member_id'=>$member_id));
					}
				}
				else
				{
					$create_new_table = $this->createOTPTable();
					if($create_new_table === true) :
						$result = mysqli_query($conn,"INSERT INTO tbl_otp_expiry(otp,is_expired,member_id, email,create_at) VALUES ('" . $otp . "', 0, '".$member_id."', '".$this->email."', '" . date("Y-m-d H:i:s"). "')");
						if($result === true) {

							echo json_encode(array('notice'=>"success", 'email'=>$this->email, 'member_id'=>$member_id));
						}
					endif;
				}

				
			}
		} else {
			echo json_encode(array('notice'=>"Email does not exists"));
		}
	}
	public function validateOTP() {
		
		$conn = dbCon();
		$result = mysqli_query($conn,"SELECT * FROM tbl_otp_expiry WHERE otp='" . $this->otp . "' AND member_id='" . $_POST['member_id'] . "' AND email='" . $_POST['email'] . "' AND is_expired!=1 AND NOW() <= DATE_ADD(create_at, INTERVAL 24 HOUR)");
		$count  = mysqli_num_rows($result);
		if(!empty($count)) {
			$result = mysqli_query($conn,"UPDATE tbl_otp_expiry SET is_expired = 1 WHERE otp = '" . $this->otp . "'");
			return true;
		} else {
			return false;
		}	
	}

}
