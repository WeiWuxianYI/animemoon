<?php $PAGE_TITLE = 'Register'; ?>
<?php include('./server.php'); ?>
<?php
    require_once("../settings/config.php");
    if (isset($loggedUser)) {
        header('location: '.$site_link.'/profile/');
        exit();
    }
?>
<?php require_once("../settings/header_footer.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Register | <?=$site_title?></title>
        <meta property="og:title" content="Register | <?=$site_title?>" />
        <meta property="og:url" content="<?=$site_link?>/account/register.php" />
        <meta property="og:description" content="Register page" />
        <meta name="description" content="Register page" />
        <meta name="keywords" content="anime, world, online, animeworld, animeworld.online" />
        <?=$head_tags?>
    </head>
    <body style="background: #181818!important;">
        <div class="body-background"></div>
        <div class="body-background-overlay"></div>
        <?=$header?>
        <div class="new-login">
            <div class="nl-title">Create an account</div>
            <div class="nt-panel">
                <?php if (isset($errors) && count($errors) > 0) {?>
                    <div class="ntp-errors">
                        <?php foreach ($errors as $error) { ?>
                            <div><?=$error?></div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="ntp-header">
                    <a href="<?=$site_link.'/account/login.php'?>">Sign in</a>
                    <a class="active">Sign up</a>
                    <a href="#" style="float: right;">Forgot</a>
                </div>
                <form method="post">
                    <div class="arrow-up" style="margin-left: 92px"></div>
                    <input name="username" type="text" placeholder="Username">
                    <input name="email" type="email" placeholder="Email">
                    <input name="password" type="password" placeholder="Password">
                    <input name="re-password" type="password" placeholder="Re-Password">
                    <button name="register" type="submit">Register</button>
                </form>
            </div>
        </div>
        <?=$footer?>
        <?php require_once("../settings/searchbar.php"); ?>
    </body>
</html>

