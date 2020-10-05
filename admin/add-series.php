<?php
$TITLE = 'ADD-SERIES';
require_once 'private/config.php';

$POST_TITLE = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$POST_ICON = filter_input(INPUT_POST, 'cover', FILTER_SANITIZE_STRING);
$POST_ANIME_LINK = filter_input(INPUT_POST, 'banner', FILTER_SANITIZE_STRING);

$POST_ANIME_STUDIO = filter_input(INPUT_POST, 'studio', FILTER_SANITIZE_STRING);
$POST_ANIME_STATUS = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
$POST_ANIME_AGE = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_STRING);

if($POST_TITLE != "" and $POST_ICON != "" and $POST_ANIME_LINK != "" and $POST_ANIME_STUDIO != "" and $POST_ANIME_STATUS != "" and $POST_ANIME_AGE != "")
{
    $title = str_replace(" ", "-", strtolower($POST_TITLE));
    $title = str_replace(":", "", $title);
    $title = str_replace(";", "", $title);
    $title = str_replace("'", "", $title);
    $title = str_replace("\"", "", $title);
    $title = str_replace(")", "", $title);
    $title = str_replace("(", "", $title);
    $title = str_replace("?", "", $title);

    if(filter_input(INPUT_POST, 'translation', FILTER_SANITIZE_STRING) == "raw" or filter_input(INPUT_POST, 'translation', FILTER_SANITIZE_STRING) == "dubbed") $title = $title."_".filter_input(INPUT_POST, 'translation', FILTER_SANITIZE_STRING);

    $INS_TITLE = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $INS_ICON = filter_input(INPUT_POST, 'cover', FILTER_SANITIZE_STRING);
    $INS_TAGS = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
    $INS_DESC = filter_input(INPUT_POST, 'desc', FILTER_SANITIZE_STRING);
    $INS_TRANSLATION = filter_input(INPUT_POST, 'translation', FILTER_SANITIZE_STRING);
    $INS_LINK = filter_input(INPUT_POST, 'banner', FILTER_SANITIZE_STRING);
    $INS_RELEASED = filter_input(INPUT_POST, 'released', FILTER_SANITIZE_STRING);

    $lowerTrans = strtolower($INS_TRANSLATION);
    switch ($lowerTrans) {
        case 'subbed': break;
        case 'dubbed': break;
        case 'raw': break;
        default:
            $msg = 'The translation can only be `subbed`, `dubbed` or `raw`';
            exit($msg);
    }

    $check = $PDOConn->prepare('SELECT * FROM `ao_index` WHERE `title`=:ti');
    $check->bindParam('ti', $title, PDO::PARAM_STR);
    $check->execute();
    if ($check->rowCount() <= 0) {
        $INS_SERIES_PDO = $PDOConn->prepare("
                INSERT INTO `ao_index` (`id`, `title`, `text_title`, `episodes`, `icon`, `tags`, `description`, `translation`, `studio`, `status`, `age`, `link`, `released`, `popular`) 
                VALUES (NULL, :title, :text_title, '0', :icon, :tags, :description, :translation, :n_studio, :n_status, :n_age, :link, :released, '0')
            ");
        $INS_SERIES_PDO->bindParam('title', $title, PDO::PARAM_STR);
        $INS_SERIES_PDO->bindParam('text_title', $INS_TITLE, PDO::PARAM_STR);
        $INS_SERIES_PDO->bindParam('icon', $INS_ICON, PDO::PARAM_STR);
        $INS_SERIES_PDO->bindParam('tags', $INS_TAGS, PDO::PARAM_STR);
        $INS_SERIES_PDO->bindParam('description', $INS_DESC, PDO::PARAM_STR);
        $INS_SERIES_PDO->bindParam('translation', $lowerTrans, PDO::PARAM_STR);
        $INS_SERIES_PDO->bindParam('n_studio', $POST_ANIME_STUDIO, PDO::PARAM_STR);
        $INS_SERIES_PDO->bindParam('n_status', $POST_ANIME_STATUS, PDO::PARAM_STR);
        $INS_SERIES_PDO->bindParam('n_age', $POST_ANIME_AGE, PDO::PARAM_STR);
        $INS_SERIES_PDO->bindParam('link', $INS_LINK, PDO::PARAM_STR);
        $INS_SERIES_PDO->bindParam('released', $INS_RELEASED, PDO::PARAM_STR);
        $INS_SERIES_PDO->execute();
    }
    header('location: add-series.php');
}
?>
<html>
<head>
    <?php require_once 'private/meta.php'; ?>
</head>
<body>
<?php require_once 'private/sidebar.php'?>
<div class="panel">
    <div class="panel-title">Add Series</div>
    <form action="" method="post" class="panel-inputs">
        <input name="title" type="text" placeholder="Title" required>
        <input name="translation" type="text" placeholder="Translation" required>
        <input name="banner" type="text" placeholder="Banner URL" required>
        <input name="cover" type="text" placeholder="Cover URL" required>
        <input name="tags" type="text" placeholder="Tags (separate by comma)" required>
        <input name="released" type="text" placeholder="Year" required>
        <input name="desc" type="text" placeholder="Description" required>
        <input name="studio" type="text" placeholder="Studio" required>
        <input name="status" type="text" placeholder="Status" required>
        <input name="age" type="text" placeholder="Age" required>
        <button type="submit">Add</button>
    </form>
</div>
</body>
</html>