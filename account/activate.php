<?php
require_once '../settings/config.php';
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
if (isset($token)) {
    $dl = $PDOConn->prepare('DELETE FROM `account_activation` WHERE `token` = :tk');
    $dl->bindParam('tk',$token, PDO::PARAM_STR);
    $dl->execute();
}
header('location: '.$site_link);