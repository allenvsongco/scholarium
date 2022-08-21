<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$root = '../';
$base = 'profile';
require($root . 'inc/setup.php');

$errmsg = $notif = '';

if (!empty($_POST)) {
    $id  = $_POST['id'];
    $tbl = $_POST['table'];
    unset($_POST['table']);

    $con = SQL('scholarium');

    if ($tbl == 'profile') {
        $username = $_POST['username'];

        $check = "SELECT username FROM $tbl WHERE id<>" . USER_ID . " AND username='$username'";
        $rs    = $con->query($check);

        if ($rs->num_rows > 0) {
            $errmsg = 'Unable to continue. Username exists.';
        }
    }

    if ($errmsg == '') {
        list($kdata, $idata, $udata) = set_kiu($_POST);
        unset($_POST);

        $qry = "INSERT INTO $tbl ($kdata) VALUES($idata) ON DUPLICATE KEY UPDATE $udata";
        $con->query($qry);

        $notif  = '<div class="box"><div class="box-content">';
        $notif .= '<h4>Your profile has been updated</h4>';
        $notif .= '</div></div>';

        echo '<META HTTP-EQUIV=Refresh CONTENT="1;URL=' . SCLR_ROOT . '/profile">';
    }

}

$_SESSION['login_type'] = 'profile';

if (!isset($_SESSION['login'])) {
    header('Location:/login');
    exit;
}

$title = ' | Profile';
$addcss = '';

if ($notif == '') {
    $body  = $root . 'inc/account.php';
}

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>
