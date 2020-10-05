<?php
    session_start();
    $style_version = '?v=5';

	$site_link = "http://domain";
	$site_title = "AnimeMoon";
	$site_main_contact = "oldadmin@example.com";

	$emailAccount = "example@domain.com";

	$site_db_link = "localhost";
	$site_db_user = "database";
	$site_db_pass = '';
	$site_db_db = "database";

	$disallowed_ips =
    "
    
	";
	
	date_default_timezone_set('UTC');
	
	if(strpos($disallowed_ips, $_SERVER['REMOTE_ADDR']) !== false)
	{
	        echo "<meta http-equiv='refresh' content='0;URL=$ac_link/banned.php'>";
	        exit();
	}

    if (isset($_GET['logout']))
    {
        unset($_SESSION['user']);
        session_destroy();
        setcookie('rememberUser', null, time()-3600);
        header("location: $site_link/account/login.php");
        exit();
    }

    try
    {
        $PDOConn = new PDO("mysql:host=$site_db_link;dbname=$site_db_db", $site_db_user, $site_db_pass);
        $PDOConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_COOKIE['rememberUser'])) {
            $cookie = $_COOKIE['rememberUser'];
            $checkSession = $PDOConn->prepare('SELECT * FROM `user_sessions` WHERE `token`=:token');
            $checkSession->bindParam('token', $cookie, PDO::PARAM_STR);
            $checkSession->execute();
            if ($checkSession->rowCount() == 1) {
                $checkSessionData = $checkSession->fetch(PDO::FETCH_ASSOC);
                $_SESSION['user'] = $checkSessionData['user'];
            } else {
                setcookie('rememberUser', null, time()-3600);
            }
            unset($checkSessionData);
            unset($checkSession);
            unset($cookie);
        }

        $usrId = $_SESSION['user'] ?? null;
        if (isset($usrId) && $usrId !== null) {
            $usrCheck = $PDOConn->prepare('SELECT * FROM users WHERE `id`=:id LIMIT 1');
            $usrCheck->bindParam('id', $usrId, PDO::PARAM_INT);
            $usrCheck->execute();
            $usrCheckFetch = $usrCheck->fetch(PDO::FETCH_ASSOC);
            if (isset($usrCheckFetch) && $usrCheck->rowCount() > 0) {
                $loggedUser = [
                    'id' => $usrCheckFetch['id'],
                    'username' => $usrCheckFetch['username'],
                    'email' => $usrCheckFetch['email'],
                    'xp' => $usrCheckFetch['xp'],
                    'icon' => $usrCheckFetch['icon'] ?? ($site_link.'\images\noav.png')
                ];
            }
        }
        unset($usrCheck);
        unset($usrId);
    }
    catch(PDOException $e) { echo "<h1>Error connecting to database!".$e."</h1>"; exit();}

    function getLevel($xp) {
        $xp = (float)$xp;
        $A = 5; $B = 50;
        $Delta = sqrt(pow($B, 2) - (4 * $A * (100 - $xp)));
        $L = (-$B + $Delta) / (2 * $A);
        $L = $L < 0 ? 0 : $L;
        return (int)$L;
    }

    function getXp($level) {
        $level = (int)$level;
        return 5 * (pow($level, 2)) + 50 * $level + 100;
    }