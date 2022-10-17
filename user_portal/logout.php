<?php
session_start();
unset($_SESSION["permission"]);
session_destroy();

header( "../user_portal/login.php");
exit;
?>
