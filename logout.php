<?php
session_start();
unset($_SESSION["admin_name"]);
setcookie('msg','');
session_destroy();

header( "Location:index.php");
exit;
?>
