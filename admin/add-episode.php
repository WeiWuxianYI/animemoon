<?php
$TITLE = 'ADD-EPS';
require_once 'private/config.php';


$getres = $PDOConn->prepare("SELECT * FROM ao_index");
$getres->execute();
$resultamount = $getres->rowCount();

$getres2 = $PDOConn->prepare("SELECT * FROM ao_episodes");
$getres2->execute();
$resultamount2 = $getres2->rowCount();

$LAST_EP = filter_input(INPUT_GET, 'oep', FILTER_SANITIZE_STRING);

$POST_ANIME = filter_input(INPUT_POST, 'anime', FILTER_SANITIZE_STRING);
$POST_EP = filter_input(INPUT_POST, 'episode', FILTER_SANITIZE_STRING);
$POST_LINK = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_STRING);

if($POST_ANIME != "" and $POST_EP != "" and $POST_LINK != "")
{
    $homepage = filter_input(INPUT_POST, 'homepage', FILTER_VALIDATE_BOOLEAN);
    if (!isset($homepage))
        $homepage = false;
    $add = "";

    if($homepage) $add = 1; else $add = 0;

    setcookie("ao_anime_cookie", $POST_ANIME);

    $UPD_ANIME_VAR = filter_input(INPUT_POST, 'anime', FILTER_SANITIZE_STRING);

    $query = $PDOConn->prepare("SELECT episodes,tags,icon,text_title FROM ao_index WHERE title = :anime");
    $query->bindParam('anime', $UPD_ANIME_VAR, PDO::PARAM_STR);
    $query->execute();
    $assoc = $query->fetch(PDO::FETCH_ASSOC);

    $num = $assoc["episodes"] + 1;
    $tags = $assoc["tags"];
    $icon = $assoc["icon"];
    $name = $assoc["text_title"];

    $UPDATE_ANIME_PDO = $PDOConn->prepare("UPDATE ao_index SET episodes = :num WHERE title = :anime");
    $UPDATE_ANIME_PDO->bindParam('num', $num, PDO::PARAM_STR);
    $UPDATE_ANIME_PDO->bindParam('anime', $UPD_ANIME_VAR, PDO::PARAM_STR);
    $UPDATE_ANIME_PDO->execute();

    $episodeID = 'episode_'.filter_input(INPUT_POST, 'episode', FILTER_VALIDATE_INT);
    $episodeTitle = 'Episode '.filter_input(INPUT_POST, 'episode', FILTER_VALIDATE_INT);
    $INS_ANIME_VAR = filter_input(INPUT_POST, 'anime', FILTER_SANITIZE_STRING);
    $INS_EP_VAR = filter_input(INPUT_POST, 'episode', FILTER_VALIDATE_INT);
    $INS_EP_LINK_VAR = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_STRING);
    $INS_EP_LINK2_VAR = filter_input(INPUT_POST, 'link2', FILTER_SANITIZE_STRING);
    $INS_EP_LINK3_VAR = filter_input(INPUT_POST, 'link3', FILTER_SANITIZE_STRING);
    $INS_EP_LINK4_VAR = filter_input(INPUT_POST, 'link4', FILTER_SANITIZE_STRING);
    $INS_EP_LINK5_VAR = filter_input(INPUT_POST, 'link5', FILTER_SANITIZE_STRING);
    $INS_EP_LINK6_VAR = filter_input(INPUT_POST, 'link6', FILTER_SANITIZE_STRING);
    $INS_EP_LINK7_VAR = filter_input(INPUT_POST, 'link7', FILTER_SANITIZE_STRING);
    $INS_EP_LINK8_VAR = filter_input(INPUT_POST, 'link8', FILTER_SANITIZE_STRING);
    $INS_TIME_VAR = time();

    $check = $PDOConn->prepare('SELECT * FROM `ao_episodes` WHERE `title`=:title AND `ep_num`=:epNum');
    $check->bindParam('title', $INS_ANIME_VAR, PDO::PARAM_STR);
    $check->bindParam('epNum', $INS_EP_VAR, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() <= 0) {
        $INSERT_ANIME_PDO = $PDOConn->prepare("
          INSERT INTO `ao_episodes` (`id`, `title`, `ep_title`, `ep_text_title`, `ep_num`, `ep_link`, `ep_link2`, `ep_link3`, `ep_link4`, `ep_link5`, `ep_link6`, `ep_link7`, `ep_link8`, `tags`, `homepage`, `time`) 
          VALUES (NULL, :anime, :ep_title, :ep_text_title, :ep_num, :ep_link, :ep_link2, :ep_link3, :ep_link4, :ep_link5, :ep_link6, :ep_link7, :ep_link8, :tags, :addH, :epTime)
        ");
        $INSERT_ANIME_PDO->bindParam('anime', $INS_ANIME_VAR, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('ep_title', $episodeID, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('ep_text_title', $episodeTitle, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('ep_num', $INS_EP_VAR, PDO::PARAM_INT);
        $INSERT_ANIME_PDO->bindParam('ep_link', $INS_EP_LINK_VAR, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('ep_link2', $INS_EP_LINK2_VAR, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('ep_link3', $INS_EP_LINK3_VAR, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('ep_link4', $INS_EP_LINK4_VAR, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('ep_link5', $INS_EP_LINK5_VAR, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('ep_link6', $INS_EP_LINK6_VAR, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('ep_link7', $INS_EP_LINK7_VAR, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('ep_link8', $INS_EP_LINK8_VAR, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('tags', $tags, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('addH', $add, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->bindParam('epTime', $INS_TIME_VAR, PDO::PARAM_STR);
        $INSERT_ANIME_PDO->execute();
    }
    header('location: add-episode.php?oep='.$POST_ANIME);
}
if (!isset($LAST_EP))
    $LAST_EP = '';
?>
<html>
<head>
    <?php require_once 'private/meta.php'; ?>
</head>
<body>
<?php require_once 'private/sidebar.php'?>
<div class="panel">
    <div class="panel-title">Add Episode</div>
    <form action="" method="post" class="panel-inputs">
        <select name='anime'>
            <?php
            $query = $PDOConn->prepare("SELECT title,text_title,translation FROM ao_index ORDER BY title ASC");
            $query->execute();
            while($assoc = $query->fetch(PDO::FETCH_ASSOC))
            {
                $title = $assoc["title"];
                $text_title = $assoc["text_title"];
                $translation = $assoc["translation"];
                $selected = "";

                if($LAST_EP == $title)
                {
                    $selected = " selected";
                }
                echo "<option value='$title'$selected>$text_title ($translation)</option>";
            }
            ?>
        </select>
        <input name="episode" type="number" placeholder="Episode" required>
        <input name="link" type="text" placeholder="Link" required>
        <input name="link2" type="text" placeholder="Link 2">
        <input name="link3" type="text" placeholder="Link 3">
        <input name="link4" type="text" placeholder="Link 4">
        <input name="link5" type="text" placeholder="Link 5">
        <input name="link6" type="text" placeholder="Link 6">
        <input name="link7" type="text" placeholder="Link 7">
        <input name="link8" type="text" placeholder="Link 8">
        <label class="checkbox">
            <input type="checkbox" name="homepage">
            <span class="box"></span>
            <span class="check"></span>
            Homepage
        </label>
        <button type="submit">Add</button>
    </form>
</div>
</body>
</html>