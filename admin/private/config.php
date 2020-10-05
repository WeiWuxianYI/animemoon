<?php
session_start();
$style_version = '?v=5';
$site_link = "http://zeno.pw/admin";
$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT.'/admin/api/apiConfig.php';

$logout = filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING);
if (isset($logout)) {
    header('HTTP/1.1 401 Unauthorized', true, 401);
    exit('<script>window.location.replace("'.$site_link.'");</script>');
}