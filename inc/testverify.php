<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

require('setup.php');

if (isset($_SESSION['testmail'])) {
    $email    = $_SESSION['testmail']['email'];
    $username = $_SESSION['testmail']['username'];
    $verif    = $_SESSION['testmail']['verif'];

} else {
    $email    = 'tester939@test.test';
    $verif     = SCLR_ROOT . '/profile/verif/?' . 'fc163d5e70d967172ea5d4dd5f4734d3c3c744a7';
    $username = 'tester939';
}

require('mail.php');
verifyEmail($email, $username, $verif);

?>
