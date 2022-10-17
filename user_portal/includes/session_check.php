<?php

if(!isset($_SESSION['permission']))
{
  session_destroy();
  header( "Location:login.php");
  exit;
}

?>
