<?php 

include("../../global/variables.php");

include(__ROOT . "/config/connection.php");


$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

if ($username == "") {
  
  $_SESSION['msg'] = "Required username";
  setcookie("msg", "Required username", time() + (60 * 30), "/");
  header("Location:". _HOST_LINK ."/index.php");
  exit();
} else if ($password == "") {
  $_SESSION['msg'] = "Required password";
  setcookie("msg", "Required password", time() + (60 * 30), "/");
 
  header("Location:". _HOST_LINK ."/index.php");
  exit;
} else {
  $qry = "select * from tbl_users where name='" . $username . "' and password='" . md5($password) . "'";

  $result = mysqli_query($mysqli, $qry);

  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['id'] = $row['id'];
    $_SESSION['admin_name'] = $row['name'];
    $_SESSION['role'] = $row['role'];
    header("Location:". _HOST_LINK ."/view/dashboard/index.php");
   
    // var_dump(gettype($row['role']));die;
      // switch ($row['role']) {
      //   case 0:
      //     setcookie("msg", "Please ask Admin for your permission.", time() + (60 * 30), "/");
      //     $_SESSION['msg'] = "Please ask Admin for your permission.";
      //     header("Location:index.php");
      //     break;
      //   case 1:
      //     $_SESSION['id'] = $row['id'];
      //     $_SESSION['admin_name'] = $row['name'];
      //     $_SESSION['role'] = $row['role'];
      //     header("Location:dashboard.php");
      //     break;
      //   case 2:
      //     $_SESSION['id'] = $row['id'];
      //     $_SESSION['admin_name'] = $row['name'];
      //     $_SESSION['role'] = $row['role'];
      //     header("Location:dashboard.php");
      //     break;
      //   default:
      //     echo 'alert("You are now being Tracked");';
      // }
      exit;
  } else {
      setcookie("msg", "Used wrong username or password", time() + (60 * 30), "/");
    $_SESSION['msg'] = "Used wrong username or password";
    header("Location:". _HOST_LINK ."/index.php");
    exit;
  }
}
