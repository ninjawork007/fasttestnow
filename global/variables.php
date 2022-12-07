<?php
$protocole = $_SERVER['REQUEST_SCHEME'] . '://';
$host = $_SERVER['HTTP_HOST'] . '/';
$project = explode('/', $_SERVER['REQUEST_URI'])[1];

$url = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($protocole . $host . $project) : ($protocole . $host);

$root = $_SERVER["DOCUMENT_ROOT"];
$myRoot = ($_SERVER['SERVER_NAME'] === 'localhost') ? ($root . "/" . $project) : $root;

define("_HOST_LINK", $url);
define("__ROOT", $myRoot);
