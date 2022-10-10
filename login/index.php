<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$root = '../';
require($root . 'inc/setup.php');

$title = ' | ' . ucwords(isset($_SESSION['login_type']) && $_SESSION['login_type'] != 'profile' ? $_SESSION['login_type'] : 'Login');
$addcss = 'login';
$body  = $root . 'inc/login.php';

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>
