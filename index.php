<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$root = '';
require('inc/setup.php');

$title = '';
$body  = 'inc/home.php';

require('inc/head.php');
require('inc/body.php');

?>
