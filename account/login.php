<?php
    $PAGE_TITLE = 'Login';
    include('./server.php');
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
        <title>Login | <?=$site_title?></title>
        <meta property="og:title" content="Login | <?=$site_title?>" />
        <meta property="og:url" content="<?=$site_link?>/account/login.php" />
        <meta property="og:description" content="Login page" />
        <meta name="description" content="Login page" />
        <meta name="keywords" content="anime, world, online, animeworld, animeworld.online" />
        <?=$head_tags?>
    </head>
    <body style="background: #181818!important;">
        <div class="body-background"></div>
        <div class="body-background-overlay"></div>
        <?=$header?>
        <div class="new-login">
            <div class="nl-title">Sign in to your account</div>
            <div class="nt-panel">
                <?php if (isset($errors) && count($errors) > 0) {?>
                    <div class="ntp-errors">
                        <?php foreach ($errors as $error) { ?>
                            <div><?=$error?></div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="ntp-header">
                    <a class="active">Sign in</a>
                    <a href="<?=$site_link.'/account/register.php'?>">Sign up</a>
                    <a href="#" style="float: right;">Forgot</a>
                </div>
                <form method="post">
                    <div class="arrow-up" style="margin-left: 18px"></div>
                    <input name="email" type="email" placeholder="Email">
                    <input name="password" type="password" placeholder="Password">
                    <div class="p-check"><input name="rememberMe" id="rememberMe" type="checkbox"><label for="rememberMe">Remember me</label></div>
                    <button name="login" type="submit">Login</button>
                </form>
            </div>
        </div>
        <?=$footer?>
        <?php require_once("../settings/searchbar.php"); ?>
    </body>
</html>

