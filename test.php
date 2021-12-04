<?php
include("config/connection.php");
ini_set('max_execution_time',-1);
$query1 = "SELECT * FROM tbl_appointment ORDER BY id ASC";
$result = mysqli_query($mysqli, $query1);
$i = 1;
while ($row = mysqli_fetch_array($result)) {
    $token = str_pad($i, 16, '0', STR_PAD_LEFT);
    $report_id = $row['id'];
    $sql = "UPDATE tbl_appointment SET user_token = '.$token.' WHERE id = $report_id";
    mysqli_query($mysqli, $sql);
    $i++;
}

echo '<pre>'; print_r($result); exit;