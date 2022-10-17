<?php
session_start();
unset($_SESSION["permission"]);
setcookie('msg','');
session_destroy();

header( "Location:index.php");
exit;
?>
