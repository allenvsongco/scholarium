<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$root = '../';
require($root . 'inc/setup.php');
// echo sha1('test' . ASIN . 'asdfasdf');

$title = ' | ' . ucwords(isset($_SESSION['login_type']) && $_SESSION['login_type'] != 'profile' ? $_SESSION['login_type'] : 'Login');
$addcss = 'login';
$body  = $root . 'inc/login.php';

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>
