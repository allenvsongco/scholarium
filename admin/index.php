<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$_SESSION['login_type'] = 'admin';

if( !isset($_SESSION['login']) ) {
    header('Location:/login' );
    exit;
}

$root = '../';
$base = basename(__DIR__);
require($root . 'inc/setup.php');

$title = ' | Admin';
$addcss = '';
$body  = $root . 'inc/admin.php';

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>
