<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$root = '../../';
$base = 'profile';
require($root . 'inc/setup.php');

$errmsg = $notif = '';

if (!empty($_POST)) {
    unset($_POST['table']);
    $username = $_POST['username'];

    $con   = SQL('scholarium');
    $tbl   = 'profile';
    $check = "SELECT username FROM $tbl WHERE username='$username'";
    $rs    = $con->query($check);

    if($rs->num_rows > 0) {
        $errmsg = 'Unable to continue. Username exists.';

    } else {
        list($kdata, $idata, $udata) = set_kiu($_POST);
        unset($_POST);

        $qry = "INSERT IGNORE INTO $tbl (id,$kdata,created_on) VALUES('',$idata,NOW())";
        $con->query($qry);

        $notif  = '<div class="box"><div class="box-content">';
        $notif .= '<h2>Congratulations! <br><br></h2>';
        $notif .= '<h4>You have been registered to ' . SCLR_FULL . '<br>Check your email for confirmation</h4>';
        $notif .= '<br><a href="/login">Login</a>';
        $notif .= '</div></div>';

        echo '<META HTTP-EQUIV=Refresh CONTENT="5;URL=' . SCLR_ROOT . '/login">';
    }

}

$_SESSION['login_type'] = 'profile';

$new = 1;
$title = ' | Create Account';
$addcss = '';

if($notif == '') {
    $body  = $root . 'inc/account.php';
}

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>
