<?php
$errmsg = null;

if (isset($_SESSION['bad_login'])) {
    $errmsg = 'invalid username or password';
    unset($_SESSION['bad_login']);
}
?>

<div class="container">
        <div class="forms-container">
            <div class="signup-signin">
                <form method="post" action="login.php" class="sign-up-form" enctype="multipart/form-data" id="login">
                    <h2 class="title">Create an Account</h2>
                    <div class="social-media">
                        <a href="#" class="social-icon fb">
                            <i class="fa fa-facebook"></i>&nbsp; &nbsp;
                            Continue with Facebook
                        </a>
                        <a href="#" class="social-icon gg">
                            <i class="fa fa-google"></i>&nbsp; &nbsp;
                            Continue with Google
                        </a>
                    </div>
                    <div>
                        <p class="social-text">- or -</p>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="username">
                    </div>
                    <div class="input-field">
                        <i class="fas fa-address-card"></i>
                        <input type="text" placeholder="first name">
                    </div>
                    <div class="input-field">
                        <i class="fas fa-address-card"></i>
                        <input type="text" placeholder="last name">
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="text" placeholder="email">
                    </div>

                       <input type="submit" value="Sign up" class="btn solid">
                </form>

                <form action="" class="sign-in-form">
                    <h2 class="title">Sign in</h2>
                    <div class="social-media">
                        <a href="#" class="social-icon fb">
                            <i class="fa fa-facebook"></i>&nbsp; &nbsp;
                            Continue with Facebook
                        </a>
                        <a href="#" class="social-icon gg">
                            <i class="fa fa-google"></i>&nbsp; &nbsp;
                            Continue with Google
                        </a>
                    </div>
                    <div>
                        <p class="social-text">- or -</p>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="username">
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" placeholder="password" minlength="8" required>
                    </div>
                       <input type="submit" value="Login" class="btn solid">
                </form>

            </div>
        </div>
        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>already have an account?</h3>
                    <p></p>
                    <button class="btn transparent" id="sign-in-btn">Sign in</button>
                </div>
                <img src="images/signup.svg" class="image" alt=""/>
            </div>

            <div class="panel right-panel">
                <div class="content">
                    <h3>don't have an account?</h3>
                    <p></p>
                    <button class="btn transparent" id="sign-up-btn">Sign up</button>
                </div>
                <img src="images/signin.svg" class="image" alt=""/>
            </div>
        </div>
    </div>