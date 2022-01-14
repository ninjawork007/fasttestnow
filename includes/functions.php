<?php
include('mailer.php');
include('database.php');

function mysqli_fetch_all_n($q) {
	$f = array();
	while($row = mysqli_fetch_assoc($q)) {
		$f[] = $row;
	}
	return $f;
}

function mysqli_fetch_array_n($q) {
	return mysqli_fetch_assoc($q);
}

function p($post) {
	$con = dbCon();
 $s = mysqli_real_escape_string($con, htmlspecialchars($post));	
 return $s;
}

function includePage($pageName) {
	echo '<div id="'.$pageName.'" style="width:85%; vertical-align:top; padding-top:30px; background-color:#fff; height:100%; display:inline-block;" >';
	include($pageName.'.php');
	echo '</div></div></div>';
}

function successMsg($msg) { 
	$html = '<div id="successMsg" style="box-shadow:0px 0px 3px 2px #777; font-family:roboto; font-size:14px; text-align:center; position:fixed; width:300px; left:0; right:0; top:0%; border-radius:0px 0px 3px 3px; background-color:#4CAF50; color:#fff; padding:5px 10px; margin:0px auto;">';
	$html .= $msg;
	$html .= '</div>';
	$html .= '<script> $(document).ready(function () { var timer = setInterval(function () { $("#successMsg").fadeOut(200); clearInterval(timer); }, 3000); } ); </script>';

	echo $html;
}

function errorMsg($msg, $error=null) { 
	$html = '<input type="text" id="errorMsgError" style="position:fixed; left:0; right:0; top:0; z-index:0; width:1px;" value="'.$error.'"/>';
	$html .= '<div id="errorMsg" style="z-index:1; box-shadow:0px 0px 3px 2px #777; font-family:roboto; font-size:14px; text-align:center; position:fixed; width:300px; left:0; right:0; top:0%; border-radius:0px 0px 3px 3px; background-color:#f44336; color:#fff; padding:5px 10px; margin:0px auto;">';
	$html .= $msg.' &nbsp; <span onclick="document.getElementById(\'errorMsgError\').select(); document.execCommand(\'copy\');" style="font-weight:bold; cursor:pointer; font-size:10px;">[COPY]</span>';
	$html .= '</div>';
	$html .= '<script> $(document).ready(function () { var timer = setInterval(function () { $("#errorMsg").fadeOut(200); $("#errorMsgError").fadeOut(200); clearInterval(timer); }, 3000); } ); </script>';

	echo $html;
}

function jsonEncode($arr) {
	echo json_encode($arr);
}

function random_string($length) {
    $key = '';
    $keys = array_merge(range(0, 9),range('a', 'z'));
    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }
    return $key;
}




?>