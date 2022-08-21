<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$root = '../';
$base = basename(__DIR__);
require($root . 'inc/setup.php');

$title = ' | Admin';
$addcss = '';
$body  = $root . 'inc/about.php';

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>
