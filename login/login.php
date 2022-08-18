<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

if( !empty($_POST) ) {

    define('INCLUDE_CHECK', 1);
    require_once('../inc/setup.php');

    $un = stripslashes(trim($_POST['un']));
    $pw = stripslashes(trim($_POST['pw']));

    $con  = SQL('edxapp');
    $pass = sha1($un . ASIN . $pw);
    $qry  = "SELECT * FROM learner_profile WHERE username='$un' AND password='$pass' AND status=1";
    $rs   = $con->query($qry);
    $r    = mysqli_fetch_array($rs);

    if( $rs->num_rows ) {
        foreach($r as $k=>$v) $$k = $v;

        $_SESSION['login']['id'] = $id;
        $_SESSION['login']['username'] = $username;
        $_SESSION['login']['name']     = "$first_name $last_name";
        $_SESSION['login']['is_admin'] = $is_admin;

        header('Location:/');
        unset($_SESSION['login_type']);
        unset($_SESSION['bad_login']);

    } else {
        $_SESSION['bad_login'] = true;
        header('Location:' . $_SERVER['HTTP_REFERER']);
    }
 
}

?>