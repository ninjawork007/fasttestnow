<?php
session_start();
date_default_timezone_set('America/Los_Angeles');

include('../includes/functions.php');
include('../libraries/SSP.php');


/** DB table to use */
// $table = <<<EOT
// SELECT tbl_appointment.id, '') AS id, IFNULL(tbl_appointment.appointment_type, '') AS appointment_type, IFNULL(tbl_appointment.firstName, '') AS firstName, IFNULL(tbl_appointment.lastName, '') AS lastName, IFNULL(tbl_appointment.phone, '') AS phone, IFNULL(tbl_appointment.email, '') AS email, IFNULL(tbl_appointment.dob, '') AS dob, IFNULL(tbl_appointment.passport_no, '') AS passport_no, IFNULL(tbl_appointment.sample_collected_date_formatted, '') AS sample_collected_date_formatted, IFNULL(tbl_appointment.created_at, '') AS created_at, IFNULL(tbl_appointment.updated_at, '') AS updated_at, IFNULL(tbl_appointment.ethnicity, '') AS ethnicity, IFNULL(tbl_appointment.address, '') AS address, IFNULL(tbl_report.user_token, '') AS patient_id, IFNULL(tbl_appointment.gender, '') AS gender FROM tbl_appointment INNER JOIN tbl_report ON tbl_appointment.email = tbl_report.patient_email
// EOT;

$table = <<<EOT
SELECT id,appointment_type,firstName,lastName,phone, email,dob, passport_no,sample_collected_date_formatted,created_at, updated_at,ethnicity,address,tbl_report.patient_id,gender FROM tbl_appointment INNER JOIN tbl_report ON tbl_appointment.email = tbl_report.patient_email
EOT;

/** Table's primary key */
$primaryKey = 'id';

/** Array of database columns which should be read and sent back to DataTables.
 * The `db` parameter represents the column name in the database, while the `dt`
 * parameter represents the DataTables column identifier. In this case simple
 * indexes */
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'firstName', 'dt' => 1),
    array('db' => 'lastName', 'dt' => 2),
    array('db' => 'dob', 'dt' => 3),
    array('db' => 'gender', 'dt' => 4),
    array('db' => 'ethnicity', 'dt' => 5),
    array('db' => 'address', 'dt' => 6),
    array('db' => 'phone', 'dt' => 7),
    array('db' => 'email', 'dt' => 8),
    array('db' => 'patient_id', 'dt' => 9),
    array('db' => 'appointment_type', 'dt' => 10),
    array('db' => 'sample_collected_date_formatted', 'dt' => 11),

);

/** SQL server connection information */
// $sql_details = array(
//     'user' => 'ftnhealt_good',
//     'pass' => '+D5EEhD#9,K;',
//     'db' => 'ftnhealt_health',
//     'host' => 'localhost'
// );
$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db' => 'ftnhealt_health',
    'host' => 'localhost:7307'
);

$where = "";
$orderBy = ($_REQUEST['order'][0]['column'] == 0) ? "ORDER BY tbl_appointment.id DESC" : null;
//$orderBy = "ORDER BY tbl_appointment.id ASC";
$dataRows = SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where, $orderBy);
echo json_encode($dataRows);
?>