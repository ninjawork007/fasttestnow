<?php

include("config/connection.php");

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);


if($username=="")
{
    setcookie("msg", "Required username.", time() + (60 * 30), "/");
  header( "Location:index.php");
  exit;

}
else if($password=="")
{
    setcookie("msg", "Required password.", time() + (60 * 30), "/");
  header( "Location:index.php");
  exit;
}
else if($email=="")
{
    setcookie("msg", "Required email.", time() + (60 * 30), "/");
    header( "Location:index.php");
    exit;
}
else
{
  $qry="select * from tbl_users where name='".$username."'";

  $result=mysqli_query($mysqli, $qry);

  if(mysqli_num_rows($result) > 0)
  {
      echo '<script> alert("a nurse with same username was registered already"); </script>';
      header( "Location:signin.php");
      exit;
  }
  else
  {
    $date_created = date('Y-m-d H:i:s');
    $date_modifed = date('Y-m-d H:i:s');
    $role = 0;
    $password = md5($password);
    $q = mysqli_query($mysqli, "INSERT INTO tbl_users (`id`, `name`, `email`, `role`, `password`, `date_created`, `date_modifed`) VALUES (NULL, '$username', '$email', '$role', '$password', '$date_created', '$date_modifed')");
    $accID = mysqli_insert_id($mysqli);
    $qb = mysqli_query($mysqli, "INSERT INTO tbl_bank (`id`, `account_id`, `balance`, `spend`) VALUES (NULL, '$accID', '0', '0')");
    setcookie("msg", "Please ask Admin for your permission.", time() + (60 * 30), "/");
    header( "Location:index.php");
    exit;

  }
}





?>
