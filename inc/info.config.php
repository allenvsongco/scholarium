<?php
	define( 'HOST', 'localhost' );
	define( 'DB', 'scholari_' );
	define( 'USN', 'scholar' );
	define( 'PSW', 'tAXnO7EGAEIo5D5n' );
	define( 'BASE_DIR', 'local.scholarium' );

	header('Access-Control-Allow-Origin: ' . BASE_DIR);
	header('Access-Control-Allow-Methods: GET, POST');
	header("Access-Control-Allow-Headers: X-Requested-With");

function SQL($dbsrc) {
	$con = mysqli_connect(HOST, DB . USN, PSW, $dbsrc);
	if (!$con) die('Connection failed: ' . mysqli_connect_error());
	return $con;
}

?>
