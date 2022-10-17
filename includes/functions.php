<?php
include('mailer.php');
include('database.php');

$locations = ['Miami - Bird Rd.
- 2067 NE 163RD ST, NORTH MIAMI BEACH, FL  33162 CLIA: 10D2214779' => 'Miami - Bird Rd.
', 'Miami Beach
- 901 PENNSYLVANIA AVE, MIAMI BEACH, FL  33139 CLIA: 10D2246198' => 'Miami Beach
', 'Aventura
- 2067 NE 163RD ST, NORTH MIAMI BEACH, FL  33162 CLIA: 10D2214779' => 'Aventura
', 'Fort Lauderdale - Oakland Park
- 1008 W OAKLAND PARK BLVD, FORT LAUDERDALE, FL  33311 CLIA: 10D2246193' => 'Fort Lauderdale - Oakland Park', 'Fort Lauderdale - Cypress Creek
- 1008 W OAKLAND PARK BLVD, FORT LAUDERDALE, FL  33311 CLIA: 10D2246193' => 'Fort Lauderdale - Cypress Creek', 'West Palm Beach
- 804 N Federal Hwy Lake Park, FL 33403 CLIA: 10D2253616' => 'West Palm Beach
', 'Orlando
- 6504 CARRIER DR 2 R, ORLANDO, FL  32819 CLIA 10D2246183' => 'Orlando
', 'Las Vegas
- 2225 E Flamingo Rd, Las Vegas NV 89119' => ' Las Vegas
', 'Concierge
- 2067 NE 163RD ST, NORTH MIAMI BEACH, FL  33162 CLIA: 10D2214779' => 'Concierge
'];

function mysqli_fetch_all_n($q)
{
	$f = array();
	while ($row = mysqli_fetch_assoc($q)) {
		$f[] = $row;
	}
	return $f;
}

function mysqli_fetch_array_n($q)
{
	return mysqli_fetch_assoc($q);
}

function p($post)
{
	$con = dbCon();
	$s = mysqli_real_escape_string($con, htmlspecialchars($post));
	return $s;
}

function getData($query)
{
	$con = dbCon();
	$q = mysqli_query($con, $query);
	return mysqli_fetch_all_n($q, MYSQLI_ASSOC);
}

function includePage($pageName)
{
	echo '<div id="' . $pageName . '" style="width:85%; vertical-align:top; padding-top:30px; background-color:#fff; height:100%; display:inline-block;" >';
	include($pageName . '.php');
	echo '</div></div></div>';
}

function successMsg($msg)
{
	$html = '<div id="successMsg" style="box-shadow:0px 0px 3px 2px #777; font-family:roboto; font-size:14px; text-align:center; position:fixed; width:300px; left:0; right:0; top:0%; border-radius:0px 0px 3px 3px; background-color:#4CAF50; color:#fff; padding:5px 10px; margin:0px auto;">';
	$html .= $msg;
	$html .= '</div>';
	$html .= '<script> $(document).ready(function () { var timer = setInterval(function () { $("#successMsg").fadeOut(200); clearInterval(timer); }, 3000); } ); </script>';

	echo $html;
}

function errorMsg($msg, $error = null)
{
	$html = '<input type="text" id="errorMsgError" style="position:fixed; left:0; right:0; top:0; z-index:0; width:1px;" value="' . $error . '"/>';
	$html .= '<div id="errorMsg" style="z-index:1; box-shadow:0px 0px 3px 2px #777; font-family:roboto; font-size:14px; text-align:center; position:fixed; width:300px; left:0; right:0; top:0%; border-radius:0px 0px 3px 3px; background-color:#f44336; color:#fff; padding:5px 10px; margin:0px auto;">';
	$html .= $msg . ' &nbsp; <span onclick="document.getElementById(\'errorMsgError\').select(); document.execCommand(\'copy\');" style="font-weight:bold; cursor:pointer; font-size:10px;">[COPY]</span>';
	$html .= '</div>';
	$html .= '<script> $(document).ready(function () { var timer = setInterval(function () { $("#errorMsg").fadeOut(200); $("#errorMsgError").fadeOut(200); clearInterval(timer); }, 3000); } ); </script>';

	echo $html;
}

function jsonEncode($arr)
{
	echo json_encode($arr);
}

function random_string($length)
{
	$key = '';
	$keys = array_merge(range(0, 9), range('a', 'z'));
	for ($i = 0; $i < $length; $i++) {
		$key .= $keys[array_rand($keys)];
	}
	return $key;
}

function hasPermission($permission)
{
	if ($_SESSION['role'] == 0)
		return true;
	$con = dbCon();
	$roleId = $_SESSION['role'];
	$permQry = "select * from role_has_permissions where role_id=$roleId";
	$permResult = mysqli_query($con, $permQry);
	$permissions = array();
	while (($permRow = mysqli_fetch_assoc($permResult))) {
		$permissions[] = $permRow['permission'];
	}
	return in_array($permission, $permissions);
}
?>