<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$root = '../../';
$base = 'profile';
require($root . 'inc/setup.php');

$errmsg = $notif = '';
?>


<main>
    <ul class="cv">
        <li>
            <ul>
                <li>NAME</li>
                <li>PERSONAL INFORMATION</li>
                <li>GENDER</li>
                <li>BIRTH</li>
            </ul>
        </li>

        <li>
            <ul>
                <li>CONTACT INFORMATION</li>
                <li>ADDRESS</li>
                <li>CONTACT</li>
            </ul>
        </li>

        <li>
            <ul>
                <li>EDUCATION INFORMATION</li>
                <li>ADDRESS</li>
                <li>CONTACT</li>
            </ul>
        </li>

        <li>
            <ul>
                <li>EMPLOYMENT INFORMATION</li>
                <li>ADDRESS</li>
                <li>CONTACT</li>
            </ul>
        </li>

        <li>
            <ul>
                <li>RELEVANT TRAININGS/SEMINARS ATTENDED</li>
                <li>ADDRESS</li>
                <li>CONTACT</li>
            </ul>
        </li>

    </ul>
</main>

<?php
$_SESSION['login_type'] = 'profile';

$title = ' | Change Password';
$addcss = '';

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>