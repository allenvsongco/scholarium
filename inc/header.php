<?php
// print_r($_SESSION);

$admin_link = $login_link = $logout_link = '';

if (isset($_SESSION['login'])) {
    $logout_link = '
    <li>
        <a href="' . SCLR_ROOT . '/profile" class="nav-action-btn" title="' . ucwords(strtolower($_SESSION['login']['name'])) . '">
            <ion-icon name="person-outline" aria-hidden="false"></ion-icon>

            <span class="nav-action-text">' . ucwords(strtolower($_SESSION['login']['username'])) . '</span>
        </a>
    </li>
    <li>
        <a href="?logout" class="nav-action-btn" title="Logout">
            <ion-icon name="exit-outline"></ion-icon>

            <span class="nav-action-text"><em>Logout</em></span>
        </a>
    </li>
    ';

    if( USER_ISADMIN ) {
        $admin_link = '
        <li class="navbar-item">
            <a href="' . SCLR_ROOT . '/admin/accounts" class="navbar-link">Accounts</a>
        </li>

        <li class="navbar-item">
            <a href="' . SCLR_ROOT . '/admin" class="navbar-link">Admin</a>
        </li>
    ';
    }

} else {

    $login_link = '
    <li>
        <a href="' . SCLR_ROOT . '/profile" class="nav-action-btn" title="Login">
            <ion-icon name="person-outline" aria-hidden="true"></ion-icon>

            <span class="nav-action-text">Login</span>
        </a>
    </li>
    ';
}

?>

    <header class="header" data-header>
        <div class="container">

            <a href="<?php echo SCLR_ROOT; ?>" class="logo">
                <h1><?php echo strtoupper(SCLR_FULL); ?></h1>
            </a>

            <div class="overlay" data-overlay></div>

            <button class="nav-open-btn" data-nav-open-btn aria-label="Open Menu">
                <ion-icon name="menu-outline"></ion-icon>
            </button>

            <nav class="navbar" data-navbar>

                <button class="nav-close-btn" data-nav-close-btn aria-label="Close Menu">
                    <ion-icon name="close-outline"></ion-icon>
                </button>

                <a href="<?php echo SCLR_ROOT; ?>" class="logo">
                    <h1><?php echo strtoupper(SCLR_FULL); ?></h1>
                </a>

                <ul class="navbar-list">

                    <li class="navbar-item">
                        <a href="<?php echo SCLR_ROOT; ?>" class="navbar-link">Home</a>
                    </li>

                    <?php echo $admin_link;?>

                </ul>

                <ul class="nav-action-list">

                    <?php echo $login_link . $logout_link; ?>

                </ul>

            </nav>

        </div>
    </header>