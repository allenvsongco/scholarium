<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

if (!isset($_SESSION['token']) || !isset($_SESSION['login']) || !$_SESSION['login']['is_admin']) {
    header('Location:/');
    exit;
}

$_SESSION['login_type'] = 'admin';

$root = '../';
$base = basename(__DIR__);
require($root . 'inc/setup.php');

$title = ' | Admin';
$addcss = '';
$body  = $root . 'inc/admin.php';

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>
