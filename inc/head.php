<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SCLR_FULL . $title; ?></title>
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/styles.css">
    <?php if (isset($addcss) && $addcss != '') echo '<link rel="stylesheet" href="' . $root . 'assets/css/' . $addcss . '.css">'; ?>
</head>

