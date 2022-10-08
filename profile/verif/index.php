<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$root = '../../';
$base = 'profile';
require($root . 'inc/setup.php');

$errmsg = $notif = '';

if (URI != '') {
    $qry = "SELECT username,first_name,email
        FROM user, profile
        WHERE hash='" . URI . "'";

    $con = SQL('scholarium');
    $rs  = $con->query($qry);
    $r   = $rs->fetch_assoc();

    if($rs->num_rows > 0) {
        $newpass = sha1(URI);
        $pass = sha1($r['username'] . ASIN . $newpass);

        $qry = "UPDATE user SET status=1,password='" . $pass . "' WHERE hash='" . URI . "'";
        $con->query($qry);

        // require($root . 'inc/mail.php');
        // welcomeEmail($r['email'],$r['first_name'], $r['username'], $newpass);
$_SESSION['testmail']['email'] = $r['email'];
$_SESSION['testmail']['first_name'] = $r['first_name'];
$_SESSION['testmail']['username'] = $r['username'];
$_SESSION['testmail']['newpass'] = $newpass;

        $notif  = '<div class="box"><div class="box-content">';
        $notif .= '<h4>Your account has been verified and activated.</h4><br>';
        $notif .= 'If you are not automatically redirected in 5 seconds,<br>';
        $notif .= 'click <a href="' . SCLR_ROOT . '/login">HERE</a> to go to the login page';
        $notif .= '</div></div>';

echo '<META HTTP-EQUIV=Refresh CONTENT="5;URL=' . $root . 'inc/testwelcome.php' . '">';
        // echo '<META HTTP-EQUIV=Refresh CONTENT="5;URL=' . SCLR_ROOT . '">';
    }
}

$_SESSION['login_type'] = 'profile';

$title = ' | Verify Account';
$addcss = '';

if ($notif == '') {
    $body  = $root . 'inc/account.php';
}

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>
