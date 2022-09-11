<?php
unset($_SESSION['testmail']);

function verifyEmail($email, $username, $verif) {
    $x  = '<li>Welcome to ' . SCLR_FULL . '!</li>';
    $x .= '<li>To verify your account, simply click on the link below or paste it into the address bar on your favorite browser:</li>';
    $x .= '<li><a href="' . $verif . '" target="_blank">' . $verif . '</a></li>';

    sendMail($email, $x);
}

function welcomeEmail($email, $name, $username, $pass) {
    $x  = '<li>Congratulations and welcome to ' . SCLR_FULL . '!</li>';
    $x .= '<li>Your account has been verified and activated. You can now fully access our website <a href="' . SCLR_ROOT . '" target="_blank">' . SCLR_ROOT . '</a>.</li>';
    $x .= '<li>Use the following credentials to login to your account:</li>';
    $x .= '<li><div>';
    $x .= '<label>Username:</label> <em>' . $username . '</em><br>';
    $x .= '<label>Password:</label> <em>' . $pass . '</em>';
    $x .= '</div></li>';

    sendMail($email, $x);
}

function sendMail($email, $msg) {
    $hdr  = 'MIME-Version: 1.0' . "\r\n";
    $hdr .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $hdr .= 'To: ' . $email . "\r\n";
    $hdr .= 'From: ' . SCLR_FULL . ' <' . NOREPLY . '>' . "\r\n";

    $style = '<style>
    body { font:normal 14px "lucida sans unicode","Lucida Grande",sans-serif;margin:0;padding:10px; }
	.welcome_email { padding:20px; }
	.welcome_email li { padding:5px 0; }
	.welcome_email,
	.welcome_email div { -moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;border-radius:5px;border:#ccc solid 1px;margin:0 20px; }
	.welcome_email div { background:#eee;padding:20px; }
	.welcome_email div label { display:inline-block; width:100px; }
    .footer { margin:0 20px;padding:10px; )}
	.welcome_email p { font-size:12px;margin:0; }
	</style>';

    $head = '<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailer</title>
' . $style . '</head>';

    $x = '<!DOCTYPE html>
    <html lang="en">
    ' . $head . '
    <body>';

    $x .= '<ul class="welcome_email" style="list-style:none;">';
    $x .= '<li><a href="<?php echo SCLR_ROOT; ?>" class="logo">
        <img src="/assets/images/logo.png" title="<?php echo strtoupper(SCLR_FULL); ?>" alt="<?php echo strtoupper(SCLR_FULL); ?>" height=80 />
    </a></li>';
    $x .= $msg;
    $x .= '<li><br>
        <p><em>This is a system generated message. DO NOT REPLY TO THIS EMAIL.</p>
        <p>To ensure reliable delivery of all emails from our system, please ensure you add ' . NOREPLY . ' to your contacts.</em></p>
    </li>'; 
    $x .= '</ul>';

    $x .= '<div class="footer">';
    $x .= '' . SCLR_FULL . ' &copy;2022-' . date('Y', time()) . ' All rights reserved.';
    $x .= '</div></body></html>';

echo $x . '<br><br>';
    // mail($email, "Welcome to " . SCLR_FULL, $msg, $hdr);
}
?>