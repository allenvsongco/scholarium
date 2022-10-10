<?php
defined('INCLUDE_CHECK') || define('INCLUDE_CHECK',  1);
date_default_timezone_set('Asia/Manila');

$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),  'https') === false ? 'http://' : 'https://';

define('SCLR_FULL', 'Scholarium');
define('SCLR_ROOT', $protocol . $_SERVER['SERVER_NAME']);
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('ADMIN_ROOT', '/admin');
define('IMG_PATH', '/assets/images/');
define('API_PATH', 'https://scholarium.tmtg-clone.click/api/');

if (isset($_SESSION['token']) && !isset($_SESSION['login'])) {
	$data = authAPI('me');

	$_SESSION['login']['id']       = $data[0]['id'];
	$_SESSION['login']['username'] = $data[0]['username'];
	$_SESSION['login']['name']     = $data[0]['first_name'] . ' ' . $data[0]['last_name'];
	$_SESSION['login']['is_admin'] = $data[0]['is_admin'];
}

define('USER_ID', isset($_SESSION['login']['id']) ? $_SESSION['login']['id'] : null);
define('USER_NAME', isset($_SESSION['login']['username']) ? $_SESSION['login']['username'] : null);
define('USER_ISADMIN', isset($_SESSION['login']['is_admin']) ? $_SESSION['login']['is_admin'] : null);

define('DOs', isset($_GET['do']) ? test_input($_GET['do']) : null);
define('SET', isset($_GET['set']) ? test_input($_GET['set']) : (isset($_SESSION['set']) ? $_SESSION['set'] : null));
define('SUBSET', substr(SET, 3));

define('Ymd', 'Y-m-d');
define('TMDSET', Ymd . ' H:i:s');
define('DATFUL', 'F d, Y');
define('DATSET', 'M d, Y');
define('TIMSET', 'h:ia');
define('ZERO', '0000-00-00 00:00:00');
define('ASIN', 'AvTFQjVqsZ3f55oF');
define('NOREPLY', 'donotreply@scholarium.io');

define('DIV_CLEAR', '<div class="clear"></div>');
define('READ_ONLY', 'readonly="readonly"');
define('DISABLED', 'disabled="disabled"');
define('SELECTED', 'selected="selected"');
define('CHECKED', 'checked="checked"');

define('URI', substr(stristr($_SERVER['REQUEST_URI'], '?'), 1));

require('info.config.php');

if( URI=='logout' ) {
	if( isset($_COOKIE[session_name()]) ) setcookie(session_name(), '', time()-3600, '/');
	$_SESSION = array();
	@session_destroy();
	header('Location:/');
}

function set_kiu($post) {
	$kdata = $idata = $udata = '';

	$ints  = "/\bid\b|is_employed|first_timer|is_active|is_partner|is_admin|status/i";

	foreach ($post as $k => $v) {
		$v = trim_escape($v);
		$$k = $v;

		$kdata .= " $k,";

		if ($k == 'last_modified') {
			$v = date(TMDSET);
		}

		$vvv = preg_match($ints, $k) ? $v : "'$v'";

		$idata .= "$vvv,";
		$udata .= $k . "=$vvv,";
	}

	$kdata = substr($kdata, 0, -1);
	$idata = substr($idata, 0, -1);
	$udata = substr($udata, 0, -1);

	return array($kdata, $idata, $udata);
}

function authAPI($endpoint) {
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => API_PATH . '/' . $endpoint,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Authorization: Bearer ' . $_SESSION['token']
		),
	));

	//Execute the cURL request. Convert response to json
	$response = json_decode(curl_exec($curl), true);

	//Check if any errors occured.
	if (curl_errno($curl)) {
		// throw the an Exception.
		throw new Exception(curl_error($curl));
	}

	curl_close($curl);

	//get the response.
	return $response['data'];

}

function test_input(&$dat) {
    return htmlspecialchars(stripslashes(trim($dat)), ENT_QUOTES);
}

function trim_escape($dat) {
	return mysqli_real_escape_string(SQL('scholarium'), stripslashes(trim($dat)));
}

?>
