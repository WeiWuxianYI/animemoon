<?php
$ROOT = $_SERVER["DOCUMENT_ROOT"];
$API_KEY = '38:5rHubT-s]DNCd#S_^+C!6$n3%r:qM';
$KITSU_IMAGES = $ROOT.'/kitsuImages';
$site_link_images = "https:/domain";
$site_link = "https://domain/admin";

$site_db_link = "localhost";
$site_db_user = "database";
$site_db_pass = '';
$site_db_db = "database";

try
{
    $PDOConn = new PDO("mysql:host=$site_db_link;dbname=$site_db_db", $site_db_user, $site_db_pass);
    $PDOConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) { echo "<h1>Error connecting to database!".$e."</h1>"; exit(); }
