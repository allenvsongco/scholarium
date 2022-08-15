<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$root = '../../';
require($root . 'inc/setup.php');

$errmsg = $notif = '';

if (!empty($_POST)) {
    list($username, $kdata, $idata, $udata) = set_kiu($_POST);

    unset($_POST);

    $con   = SQL('edxapp');
    $tbl   = 'learner_profile';
    $check = "SELECT username FROM $tbl WHERE id<>" . URI . " AND username='$username'";
    $rs    = $con->query($check);

    if ($rs->num_rows > 0) {
        $errmsg = 'Unable to continue. Username exists.';

    } else {
        $qry = "INSERT INTO $tbl ($kdata) VALUES($idata) ON DUPLICATE KEY UPDATE $udata";
        $con->query($qry);

        $notif  = '<div class="box"><div class="box-content">';
        $notif .= "<h4>$username's profile has been updated</h4>";
        $notif .= '</div></div>';

        echo '<META HTTP-EQUIV=Refresh CONTENT="3;URL=' . SCLR_ROOT . '/admin/accounts">';
    }
}

$_SESSION['login_type'] = 'profile';

if (!isset($_SESSION['login'])) {
    header('Location:/login');
    exit;
}

$user = URI;
$title = ' | Accounts';
$addcss = '';

if (URI != '') {
    if ($notif == '') {
        $body  = $root . 'inc/account.php';
    }

} else {
    $body  = $root . 'inc/accounts.php';
}

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>
