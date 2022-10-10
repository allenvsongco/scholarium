<?php
$errmsg = null;

if (isset($_SESSION['bad_login'])) {
    $errmsg = 'invalid username or password';
    unset($_SESSION['bad_login']);
}
?>

<div class="box">
    <div class="box-content">
        <form method="post" action="login.php" enctype="multipart/form-data" id="login">
            <ul>
                <li><input type="text" name="user" placeholder="Username" required /></li>
                <li><input type="password" name="pass" placeholder="Password" minlength="8" required /></li>
                <li><input type="submit" name="submit" value="Login" /></li>
                <?php if (!isset($_SESSION['login_type']) || $_SESSION['login_type'] != 'admin') { ?>
                    <li><a href="<?php echo strtoupper(SCLR_ROOT); ?>/profile/new" class="lite">Create an account</a> | <a href="#" class="lite">Forgot password?</a></li>
                <?php } ?>
                <li class="bad"><?php echo $errmsg; ?></li>
            </ul>
        </form>
    </div>
</div>