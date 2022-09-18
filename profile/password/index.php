<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

$root = '../../';
$base = 'profile';
require($root . 'inc/setup.php');

$errmsg = $notif = '';
$good = 0;

if (!empty($_POST)) {
    $un    = USER_NAME;

    $oldpw = sha1($un . ASIN . stripslashes(trim($_POST['oldpw'])));
    $newpw = stripslashes(trim($_POST['newpw']));
    $confirm = stripslashes(trim($_POST['confirm']));

    $con  = SQL('scholarium');
    $qry  = "SELECT password FROM profile WHERE username='$un' AND password='$oldpw' AND status=1";
    $pass   = $con->query($qry);

    if ($pass->num_rows == 0) {
        $errmsg = 'Unable to continue. Old password mismatch.';

    } elseif ($newpw != $confirm) {
        $errmsg = 'Unable to continue. New password mismatch.';

    } else {
        $pass = sha1($un . ASIN . $newpw);
        $qry  = "UPDATE profile SET password='$pass' WHERE username='$un'";
        $con->query($qry);

        $errmsg = 'Password successfully changed.';
        $good   = 1;
    }

    unset($_POST);
    echo '<META HTTP-EQUIV=Refresh CONTENT="1;URL=' . SCLR_ROOT . '/profile' . '">';
}
?>

<div class="box">
    <div class="box-content">
        <form method="post" class="change-pass">
            <ul>
                <li><label>Old password</label> <input type="password" name="oldpw" placeholder="Old password" required /></li>
                <li><label>New password</label> <input type="password" name="newpw" placeholder="New password" minlength="8" required /></li>
                <li><label>Confirm password</label> <input type="password" name="confirm" placeholder="Confirm password" minlength="8" required /></li>
                <li>
                    <hr>
                </li>
                <li class="rt"><span class="<?php echo ($good ? 'good' : 'bad') ?>"><?php echo $errmsg . $notif; ?></span> <input type="submit" name="submit" value="Change Password" /></li>
            </ul>
        </form>
    </div>
</div>

<?php
$_SESSION['login_type'] = 'profile';

$title = ' | Change Password';
$addcss = '';

require($root . 'inc/head.php');
require($root . 'inc/body.php');

?>