<?php
session_start();
date_default_timezone_set('America/Los_Angeles');

include('includes/functions.php');
$method = '';
if(isset($_POST['method'])) { $method = p($_POST['method']); }

switch ($method) {
    case 'setUserRole':
        set_user_role($_POST['id'], $_POST['role']);
        break;
    default: echo 'alert("You are now being Tracked");'; die;
}

function set_user_role($id, $role) {
    $con = dbCon();
    
    $q = mysqli_query($con, "UPDATE tbl_users SET role=$role WHERE id =$id;");
    echo json_encode($q);
}