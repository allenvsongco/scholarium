<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

require('setup.php');

if (!isset($base) || $base == '') {
    $email    = 'test4@test.test';
    $verif     = SCLR_ROOT . '/profile/verif/?' . '6c4650fefdb4f915669c6b266fc074370b605345';
    $username = 'test4';
}

require('mail.php');
verifyEmail($email, $username, $verif);

?>
