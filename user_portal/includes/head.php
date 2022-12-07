<?php 

$protocole = $_SERVER['REQUEST_SCHEME'] . '://';
$host = $_SERVER['HTTP_HOST'] . '/';
$project = explode('/', $_SERVER['REQUEST_URI'])[1];

$url = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($protocole . $host . $project) : ($protocole . $host);

$root = $_SERVER["DOCUMENT_ROOT"];
$myRoot = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($root . "/" . $project) : $root;



include($myRoot . "/user_portal/config/connection.php");
include($myRoot . "/user_portal/includes/session_check.php");
include($myRoot . '/user_portal/includes/functions.php');


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
  <link rel="shortcut icon" href="<?php echo $url; ?>/assets/images/favicon.ico">