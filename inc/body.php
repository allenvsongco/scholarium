<body id="top">

    <?php require_once($root . 'inc/header.php'); ?>
    <?php if (isset($body) != '') require_once($body); ?>
    <?php if (isset($notif) != '') echo $notif; ?>

    <a href="#top" class="go-top-btn" data-go-top>
        <ion-icon name="arrow-up-outline"></ion-icon>
    </a>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script src="<?php echo $root; ?>assets/js/script.js"></script>

</body>

</html>