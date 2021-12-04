<?php
session_start();
date_default_timezone_set('America/Los_Angeles');

include('includes/functions.php');
$method = '';
if(isset($_POST['method'])) { $method = p($_POST['method']); }

switch ($method) {
    case 'antigen':
        antigen_data();
        break;
    case 'visby':
        visby_data();
        break;
    case 'accula':
        accula_data();
        break;
    case 'antibody':
        antibody_data();
        break;
    case 'flu':
        flu_data();
        break;
    case 'requisition_form':
        requisition_forms();
        break;        
    case 'all':
        all_data();
        break;
    
    default: echo 'alert("You are now being Tracked");'; die;
}

function antigen_data() {
    $con = dbCon();
    if($_SESSION["role"] == 1) {
        $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id WHERE type_id=2 ORDER BY tbl_report.handled_at DESC;");
    }
    if($_SESSION["role"] == 2) {
        $user_id = $_SESSION['id'];
        $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id WHERE type_id=2 AND user_id=$user_id ORDER BY tbl_report.handled_at DESC;");
    }
    $fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);
    
    echo json_encode($fetch);
}
function visby_data() {
    $con = dbCon();

    if($_SESSION["role"] == 1) {
        $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id WHERE type_id=1 ORDER BY tbl_report.handled_at DESC;");
    }
    if($_SESSION["role"] == 2) {
        $user_id = $_SESSION['id'];
        $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id WHERE type_id=1 AND user_id=$user_id ORDER BY tbl_report.handled_at DESC;");
    }
    $fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);

    echo json_encode($fetch);
}
function accula_data() {
    $con = dbCon();

    if($_SESSION["role"] == 1) {
        $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id WHERE type_id=3 ORDER BY tbl_report.handled_at DESC;");
    }
    if($_SESSION["role"] == 2) {
        $user_id = $_SESSION['id'];
        $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id WHERE type_id=3 AND user_id=$user_id ORDER BY tbl_report.handled_at DESC;");
    }
    $fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);

    echo json_encode($fetch);
}
function antibody_data() {
    $con = dbCon();

    if($_SESSION["role"] == 1) {
        $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id WHERE type_id=4 ORDER BY tbl_report.handled_at DESC;");
    }
    if($_SESSION["role"] == 2) {
        $user_id = $_SESSION['id'];
        $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id WHERE type_id=4 AND user_id=$user_id ORDER BY tbl_report.handled_at DESC;");
    }
    $fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);

    echo json_encode($fetch);
}
function flu_data() {
    $con = dbCon();
    if($_SESSION["role"] == 1) {
        $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id WHERE type_id=5 ORDER BY tbl_report.handled_at DESC;");
    }
    if($_SESSION["role"] == 2) {
        $user_id = $_SESSION['id'];
        $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id WHERE type_id=5 AND user_id=$user_id ORDER BY tbl_report.handled_at DESC;");
    }
    $fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);
    
    echo json_encode($fetch);
}
function all_data() {
    $con = dbCon();

    $q = mysqli_query($con, "SELECT tbl_report.*, tbl_type.name FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id=tbl_type.id ORDER BY tbl_report.handled_at DESC;");
    $fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);

    echo json_encode($fetch);
}
function requisition_forms() {
    $con = dbCon();
    $q = mysqli_query($con, "SELECT * tbl_appointment ORDER BY id DESC");
    $fetch = mysqli_fetch_all_n($q, MYSQLI_ASSOC);

    echo json_encode($fetch);
}



?>