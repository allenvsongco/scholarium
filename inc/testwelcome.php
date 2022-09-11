<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

require('setup.php');

if (isset($_SESSION['testmail'])) {
    $email    = $_SESSION['testmail']['email'];
    $name     = $_SESSION['testmail']['first_name'];
    $username = $_SESSION['testmail']['username'];
    $pass     = $_SESSION['testmail']['newpass'];

} else {
    $email    = 'tester939@test.test';
    $name     = 'test';
    $username = 'tester939';
    $pass     = 'cb65cf3c973f93e428d12c7d5bf1d99b147b0a23';
}

require('mail.php');
welcomeEmail($email, $name, $username, $pass);

?>
