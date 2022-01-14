<?php include("config/connection.php");
include("includes/session_check.php");
include('functions.php');

$setting_qry="SELECT * FROM tbl_settings where id='1'";
$setting_result=mysqli_query($mysqli,$setting_qry);
$settings_details=mysqli_fetch_assoc($setting_result);

define("ONESIGNAL_APP_ID",$settings_details['onesignal_id']);
define("ONESIGNAL_REST_KEY",$settings_details['onesignal_rest_key']);

$currentFile = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentFile);
$currentFile = $parts[count($parts) - 1];

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <title>HEALTH</title>
  <meta content="Admin Dashboard" name="description" />
  <meta content="Themesbrand" name="author" />
  <link rel="shortcut icon" href="assets/images/favicon.ico">
