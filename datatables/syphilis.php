<?php
session_start();
date_default_timezone_set('America/Los_Angeles');

include('../includes/functions.php');
include('../libraries/SSP.php');


/** DB table to use */
// $table = <<<EOT
// SELECT IFNULL(tbl_report.report_id, '') AS report_id, IFNULL(tbl_report.type_id, '') AS type_id, IFNULL(tbl_report.patient_firstname, '') AS patient_firstname, IFNULL(tbl_report.patient_lastname, '') AS patient_lastname, IFNULL(tbl_report.patient_phone, '') AS patient_phone, IFNULL(tbl_report.patient_email, '') AS patient_email, IFNULL(tbl_report.patient_birth, '') AS patient_birth, IFNULL(tbl_report.patient_passport, '') AS patient_passport, IFNULL(tbl_report.sample_taken, '') AS sample_taken, IFNULL(tbl_report.handled_at, '') AS handled_at, IFNULL(tbl_report.pdf_file_url, '') AS pdf_file_url, IFNULL(tbl_report.pdf_file_name, '') AS pdf_file_name, IFNULL(tbl_report.report_created_at, '') AS report_created_at, IFNULL(tbl_report.report_updated_at, '') AS report_updated_at, IFNULL(tbl_type.name, '') AS `name`, IFNULL(IF(tbl_report.report_results = 0, 'Negative', 'Positive'), '') AS report_results FROM tbl_report INNER JOIN tbl_type ON tbl_report.type_id = tbl_type.id 
// EOT;
// $table = <<<EOT
// SELECT id, CONCAT(firstname, ' ', lastname) AS fullname, IF(shipping_service = 'office', 'Send to office', 'Send to patient') AS service, phone, email, IF(semaglutide_check = 0, 'No', 'Yes') AS semaglutide, quantity, insulin_qty, notes, handled_at FROM  tbl_rx_order
// EOT;
$table = <<<EOT
SELECT id, firstname, lastname, appointment_id, email, results, dob, handled_at, pdfLink FROM  tbl_syphilis
EOT;
/** Table's primary key */
$primaryKey = 'id';

/** Array of database columns which should be read and sent back to DataTables.
 * The `db` parameter represents the column name in the database, while the `dt`
 * parameter represents the DataTables column identifier. In this case simple
 * indexes */
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'firstname', 'dt' => 1),
    array('db' => 'email', 'dt' => 2),
    array('db' => 'dob', 'dt' => 3),
    array('db' => 'results', 'dt' => 4),
    array('db' => 'handled_at', 'dt' => 5),
    array('db' => 'appointment_id', 'dt' => 6),
    array('db' => 'pdfLink', 'dt' => 7),
    array('db' => 'lastname', 'dt' => 8)
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
$orderBy = ($_REQUEST['order'][0]['column'] == 0) ? "ORDER BY tbl_syphilis.id DESC" : null;
//$orderBy = "ORDER BY tbl_report.report_id DESC";
$dataRows = SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where, $orderBy);
echo json_encode($dataRows);
?>