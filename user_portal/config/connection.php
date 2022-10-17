<?php
error_reporting(0);
ob_start();
session_start();

header("Content-Type: text/html;charset=UTF-8");

// DEFINE ('DB_USER', 'ftnhealt_good');
// DEFINE ('DB_PASSWORD', '+D5EEhD#9,K;');
// DEFINE ('DB_HOST', 'localhost');
// DEFINE ('DB_NAME', 'ftnhealt_health');
DEFINE ('DB_USER', 'root');
DEFINE ('DB_PASSWORD', '');
DEFINE ('DB_HOST', 'localhost:7307');
DEFINE ('DB_NAME', 'ftnhealt_health');


$mysqli=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME) or die("Not connected.");

mysqli_query($mysqli,"SET NAMES 'utf8'");
