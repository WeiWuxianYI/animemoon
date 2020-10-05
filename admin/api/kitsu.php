<?php
require_once 'apiConfig.php';
$key = filter_input(INPUT_POST, 'key', FILTER_SANITIZE_STRING);
if ($key != $API_KEY)
    exit('error');
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$txtTitle = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$desc = filter_input(INPUT_POST, 'desc', FILTER_SANITIZE_STRING);
$cover = filter_input(INPUT_POST, 'cover', FILTER_SANITIZE_STRING);
$banner = filter_input(INPUT_POST, 'banner', FILTER_SANITIZE_STRING);
$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
$age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_STRING);
$tags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
$studio = filter_input(INPUT_POST, 'studio', FILTER_SANITIZE_STRING);
$translation = filter_input(INPUT_POST, 'translation', FILTER_SANITIZE_STRING);
$slug = filter_input(INPUT_POST, 'slug', FILTER_SANITIZE_STRING);
$altT = filter_input(INPUT_POST, 'altT', FILTER_SANITIZE_STRING);
if (!isset($altT))
    $altT = '';
if (!isset($id))
    exit('error id');
if (!isset($txtTitle))
    exit('error title');
if (!isset($desc))
    exit('error desc');
if (!isset($cover))
    exit('error cover');
if (!isset($date))
    exit('error date');
if (!isset($status))
    exit('error status');
if (!isset($age))
    exit('error age');
if (!isset($tags))
    exit('error tags');
if (!isset($slug))
    exit('error slug');
if (!isset($studio))
    $studio = '';
if (!isset($translation) || $translation === '')
    exit('error translation');

if (!isset($banner) || trim($banner) == '')
    $banner = "https://animemoon.net/img/anime/newicons/no_wp.jpg";

$translation = strtolower($translation);
if (!($translation === 'subbed' || $translation === 'dubbed' || $translation === 'raw'))
    exit('error translationName');
$coverExtension = pathinfo((explode('?', $cover)[0]), PATHINFO_EXTENSION);
$bannerExtension = pathinfo((explode('?', $banner)[0]), PATHINFO_EXTENSION);
if (!isset($coverExtension, $bannerExtension))
    exit('error extension');
$coverPath = $KITSU_IMAGES.'/cover_'.$id.'.'.$coverExtension;
$bannerPath = $KITSU_IMAGES.'/banner_'.$id.'.'.$bannerExtension;
file_put_contents($coverPath, fopen($cover, 'r'));
file_put_contents($bannerPath, fopen($banner, 'r'));

$coverPublicPath = $site_link_images.'/kitsuImages/cover_'.$id.'.'.$coverExtension;
$bannerPublicPath = $site_link_images.'/kitsuImages/banner_'.$id.'.'.$bannerExtension;

if($translation == "raw" or $translation == "dubbed")
    $slug = $slug."_".$translation;

$slug = str_replace('?', '', $slug);
$slug = str_replace('/', '', $slug);
$slug = str_replace(":", "", $slug);
$slug = str_replace(";", "", $slug);
$slug = str_replace("'", "", $slug);
$slug = str_replace("\"", "", $slug);
$slug = str_replace(")", "", $slug);
$slug = str_replace("(", "", $slug);

$INS_SERIES_PDO = $PDOConn->prepare("
            INSERT INTO `ao_index` (`id`, `title`, `text_title`, `alt_title`, `episodes`, `icon`, `tags`, `description`, `translation`, `studio`, `status`, `age`, `link`, `released`, `popular`) 
            VALUES (NULL, :title, :text_title, :alt, '0', :icon, :tags, :description, :translation, :n_studio, :n_status, :n_age, :link, :released, '0')
        ");
$INS_SERIES_PDO->bindParam('title', $slug, PDO::PARAM_STR);
$INS_SERIES_PDO->bindParam('text_title', $txtTitle, PDO::PARAM_STR);
$INS_SERIES_PDO->bindParam('alt', $altT, PDO::PARAM_STR);
$INS_SERIES_PDO->bindParam('icon', $coverPublicPath, PDO::PARAM_STR);

$INS_SERIES_PDO->bindParam('tags', $tags, PDO::PARAM_STR);
$INS_SERIES_PDO->bindParam('description', $desc, PDO::PARAM_STR);
$INS_SERIES_PDO->bindParam('translation', $translation, PDO::PARAM_STR);

$INS_SERIES_PDO->bindParam('n_studio', $studio, PDO::PARAM_STR);
$INS_SERIES_PDO->bindParam('n_status', $status, PDO::PARAM_STR);
$INS_SERIES_PDO->bindParam('n_age', $age, PDO::PARAM_STR);

$INS_SERIES_PDO->bindParam('link', $bannerPublicPath, PDO::PARAM_STR);
$INS_SERIES_PDO->bindParam('released', $date, PDO::PARAM_STR);
$INS_SERIES_PDO->execute();

exit('success');