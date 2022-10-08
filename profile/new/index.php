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
    foreach ($_POST as $k => $v) $$k = $v;
    unset($_POST);

    $con = SQL('scholarium');
    $tbl = 'user';

    $usercheck = $con->query("SELECT username FROM $tbl WHERE username='$username'");
    $mailcheck = $con->query("SELECT email FROM $tbl WHERE email='$email'");

    if ($usercheck->num_rows > 0) {
        $errmsg = 'Unable to continue. Username exists.';

    } elseif ($mailcheck->num_rows > 0) {
        $errmsg = 'Unable to continue. Email exists.';

    } else {
        $hash  = sha1($username . ASIN . $email);
        $verif = SCLR_ROOT . '/profile/verif/?' . $hash;

        $qry = "INSERT IGNORE INTO $tbl (id,username,email,date_joined,hash) VALUES('','$username','$email',NOW(),'$hash')";
        $con->query($qry);
        $last_id = $con->insert_id;

        $qry = "INSERT IGNORE INTO profile (id,first_name,middle_name,last_name,last_modified) VALUES($last_id,'$first_name','$middle_name','$last_name',NOW())";
        $con->query($qry);

        // require($root . 'inc/mail.php');
        // verifyEmail($email, $username, $verif);
$_SESSION['testmail']['email'] = $email;
$_SESSION['testmail']['username'] = $username;
$_SESSION['testmail']['verif'] = $verif;

        $notif  = '<div class="box"><div class="box-content">';
        $notif .= '<h3>Congratulations ' . $username . '!</h3><br>';
        $notif .= '<h4>You have been registered to ' . SCLR_FULL . '</h4><br>';
        $notif .= 'Check your email to verify and activate your account';
        $notif .= '</div></div>';

echo '<META HTTP-EQUIV=Refresh CONTENT="5;URL=' . $root . 'inc/testverify.php' . '">';
        // echo '<META HTTP-EQUIV=Refresh CONTENT="5;URL=' . SCLR_ROOT . '/login">';
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
