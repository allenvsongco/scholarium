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
        FROM profile
        WHERE hash='" . URI . "'";

    $con = SQL('scholarium');
    $rs  = $con->query($qry);
    $r   = $rs->fetch_assoc();

    if($rs->num_rows > 0) {
        $newpass = sha1(URI);
        $pass = sha1($r['username'] . ASIN . $newpass);

        $qry = "UPDATE profile SET status=1,password='" . $pass . "' WHERE hash='" . URI . "'";
        $con->query($qry);

        // require($root . 'inc/mail.php');
        // welcomeEmail($r['email'],$r['first_name'], $r['username'], $newpass);

        $notif  = '<div class="box"><div class="box-content">';
        $notif .= '<h4>Your account has been verified and activated.</h4><br>';
        $notif .= 'If you are not automatically redirected in 5 seconds,';
        $notif .= 'click <a href="' . SCLR_ROOT . '">HERE</a> to go to the home page';
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
