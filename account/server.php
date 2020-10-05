<?php
require_once("../settings/config.php");

$username = "";
$email    = "";
$errors = array();

$isLogin = filter_input(INPUT_POST, 'login', FILTER_UNSAFE_RAW);
$isRegister = filter_input(INPUT_POST, 'register', FILTER_UNSAFE_RAW);

if (isset($isRegister))
{
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password_1 = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    $password_2 = filter_input(INPUT_POST, 're-password', FILTER_UNSAFE_RAW);

    if (empty($username) || !isset($password_1)) array_push($errors, "Username is required");
    if (empty($email) || !isset($password_1)) array_push($errors, "Email is required");
    if (empty($password_1) || !isset($password_1)) array_push($errors, "Password is required");
    if (empty($password_2) || !isset($password_2)) array_push($errors, "Password is required");
    if ($password_1 != $password_2) array_push($errors, "The two passwords do not match");

    $result = $PDOConn->prepare("SELECT * FROM users WHERE username = :usr OR email = :eml LIMIT 1");
    $result->bindParam('usr',$username, PDO::PARAM_STR);
    $result->bindParam('eml',$email, PDO::PARAM_STR);
    $result->execute();
    $user = $result->fetch(PDO::FETCH_ASSOC);

    if ($result->rowCount() > 0)
    {
        if ($user['username'] == $username) array_push($errors, "Username already exists");
        if ($user['email'] == $email) array_push($errors, "Email already exists");
    }
    if (count($errors) <= 0)
    {
        $password = password_hash($password_1, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, `password`) 
                  VALUES(:usr, :eml, :pass)";

        $INSERT_PDO_USER = $PDOConn->prepare($query);
        $INSERT_PDO_USER->bindParam('usr', $username, PDO::PARAM_STR);
        $INSERT_PDO_USER->bindParam('eml', $email, PDO::PARAM_STR);
        $INSERT_PDO_USER->bindParam('pass', $password, PDO::PARAM_STR);
        $INSERT_PDO_USER->execute();

        $uid = $PDOConn->lastInsertId();
        $token = bin2hex(openssl_random_pseudo_bytes(110)) . time();
        $userAct = $PDOConn->prepare('INSERT INTO `account_activation` (`id`, `token`) VALUES (:us, :tk)');
        $userAct->bindParam('us', $uid, PDO::PARAM_INT);
        $userAct->bindParam('tk', $token, PDO::PARAM_STR);
        $userAct->execute();

        $msg =
            "
            To activate your account on AnimeMoon use the text url: 
            $site_link/account/activate.php?token=$token
            ";
        $headers =
            'From: ' . $emailAccount . "\r\n" .
            'Reply-To: ' . $emailAccount . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail($email, 'Email verification', $msg, $headers);

        header('location: '.$site_link.'/account/login.php');
        exit();
    }
}
if (isset($isLogin))
{
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $remMe = filter_input(INPUT_POST, 'rememberMe', FILTER_VALIDATE_BOOLEAN) ?? false;
    if (empty($email) || !isset($email)) array_push($errors, "Email is required");
    if (empty($password) || !isset($password)) array_push($errors, "Password is required");
    if (count($errors) == 0)
    {
        $results = $PDOConn->prepare("SELECT * FROM users WHERE email = :email");
        $results->bindParam('email', $email, PDO::PARAM_STR);
        $results->execute();
        $assoc = $results->fetch(PDO::FETCH_ASSOC);
        $userId = $assoc['id'];

        $checkVer = $PDOConn->prepare('SELECT * FROM `account_activation` WHERE `id` = :uid');
        $checkVer->bindParam('uid', $userId, PDO::PARAM_INT);
        $checkVer->execute();
        if (password_verify($password, $assoc["password"])) {
            if ($checkVer->rowCount() > 0) {
                array_push($errors, "The account is not verified.");
            } else {
                $_SESSION['user'] = $userId;
                if ($remMe) {
                    $token = bin2hex(openssl_random_pseudo_bytes(256));
                    $setToken = $PDOConn->prepare("
                        INSERT INTO `user_sessions` (`user`, `token`) VALUES (:usr, :token) 
                        ON DUPLICATE KEY UPDATE `token`=:token");
                    $setToken->bindParam('usr', $userId, PDO::PARAM_INT);
                    $setToken->bindParam('token', $token, PDO::PARAM_STR);
                    $setToken->execute();
                    setcookie('rememberUser', $token, time() + 31536000);
                }
                header('location: ' . $site_link . '/profile/');
                exit();
            }
        } else array_push($errors, "Wrong email/password combination");
    }
}