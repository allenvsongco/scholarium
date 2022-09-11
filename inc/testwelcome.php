<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

require('setup.php');

if (!isset($base) || $base == '') {
    $email    = 'test4@test.test';
    $name     = 'test';
    $username = 'test4';
    $pass     = 'f2acff539a47d6c78232bba752d427f7b53fcb34';
}

require('mail.php');
welcomeEmail($email, $name, $username, $pass);

?>
