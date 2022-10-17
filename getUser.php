<?php
include('includes/functions.php');
include('./UserDetailClass.php');

$method = '';
if (isset($_POST['method'])) {
    $method = p($_POST['method']);
}

switch ($method) {
    case 'getUser':
        $fName = $_POST['firstName'];
        $lName = $_POST['lastName'];
        $email = $_POST['email'];
        $user = new UserDetail($fName, $lName, $email);
        echo json_encode($user->getUserTotalInfo());
        exit;
    
    default:
        echo '<script> alert("No form action"); </script>';
        die;
}