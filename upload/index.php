<?php
// This PHP script must be in "SOME_PATH/jsonFile/index.php"
// phpinfo();
ob_start();

$data = $_REQUEST['post-form'];
$json = json_decode($data);
// var_dump($json);die;
//write json to file
ob_end_clean();
    echo "This output will be sent to the browser";

if (file_put_contents("../ajax/data/sampledata.json", json_encode($json)))
    echo json_encode(array('data', 'success'));
else 
    echo "Oops! Error creating json file...";
    
?>