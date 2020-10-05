<?php
$TITLE = 'DEL-EPS';
require_once 'private/config.php';


$POST_DEL_ANIME = filter_input(INPUT_POST, 'del_anime', FILTER_SANITIZE_STRING);
$POST_DEL_EP = filter_input(INPUT_POST, 'del_episode', FILTER_SANITIZE_STRING);

$LAST_EP = filter_input(INPUT_GET, 'oep', FILTER_SANITIZE_STRING);

if($POST_DEL_ANIME != "" and $POST_DEL_EP != "")
{
    $DEL_ANIME_PDO = filter_input(INPUT_POST, 'del_anime', FILTER_SANITIZE_STRING);
    $query = $PDOConn->prepare("SELECT episodes FROM ao_index WHERE title = :title");
    $query->bindParam('title',$DEL_ANIME_PDO, PDO::PARAM_STR);
    $query->execute();
    $assoc = $query->fetch(PDO::FETCH_ASSOC);

    $num = $assoc["episodes"] - 1;

    $UPD_DEL_ANIME_PDO = $PDOConn->prepare("UPDATE ao_index SET episodes = :num WHERE title = :title");
    $UPD_DEL_ANIME_PDO->bindParam('title',$DEL_ANIME_PDO, PDO::PARAM_STR);
    $UPD_DEL_ANIME_PDO->bindParam('num',$num, PDO::PARAM_STR);
    $UPD_DEL_ANIME_PDO->execute();

    $DEL_ANIME_PDO = $PDOConn->prepare("DELETE FROM ao_episodes WHERE title = :title AND ep_num = :num");
    $DEL_ANIME_PDO->bindParam('title',$POST_DEL_ANIME, PDO::PARAM_STR);
    $DEL_ANIME_PDO->bindParam('num',$POST_DEL_EP, PDO::PARAM_STR);
    $DEL_ANIME_PDO->execute();
    header('location: delete-episode.php?oep='.$POST_DEL_ANIME);
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
    <div class="panel-title">Delete Episode</div>
    <form action="" method="post" class="panel-inputs">
        <select name='del_anime'>
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
        <input name="del_episode" type="number" placeholder="Episode" required>
        <button type="submit">Delete</button>
    </form>
</div>
</body>
</html>