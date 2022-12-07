<?php
session_start();
unset($_SESSION["permission"]);
setcookie('msg','');
session_destroy();

$protocole = $_SERVER['REQUEST_SCHEME'] . '://';
$host = $_SERVER['HTTP_HOST'] . '/';
$project = explode('/', $_SERVER['REQUEST_URI'])[1];

$url = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($protocole . $host . $project) : ($protocole . $host);

header( "Location:". $url ."/index.php");
exit;
?>
