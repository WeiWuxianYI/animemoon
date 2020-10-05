<?php
$TITLE = 'EDIT-EPS';
require_once 'private/config.php';

$POST_EDIT_ANIME = filter_input(INPUT_POST, 'edit_anime', FILTER_SANITIZE_STRING);
$POST_EDIT_EP = filter_input(INPUT_POST, 'edit_episode', FILTER_SANITIZE_STRING);

$LAST_EP = filter_input(INPUT_GET, 'oep', FILTER_SANITIZE_STRING);

if($POST_EDIT_ANIME != "" and $POST_EDIT_EP != "")
{
    $homepage = filter_input(INPUT_POST, 'edit_homepage', FILTER_VALIDATE_BOOLEAN);
    if (!isset($homepage))
        $homepage = false;
    $add = "";
    $link = "";

    if($homepage)
        $add = 1;
    else
        $add = 0;

    if(filter_input(INPUT_POST, 'edit_link', FILTER_SANITIZE_STRING) != "")
    {
        $link = ", ep_link = '".filter_input(INPUT_POST, 'edit_link', FILTER_SANITIZE_STRING)."'";
    }
    if(filter_input(INPUT_POST, 'edit_link2', FILTER_SANITIZE_STRING) != "")
    {
        $link .= ", ep_link2 = '".filter_input(INPUT_POST, 'edit_link2', FILTER_SANITIZE_STRING)."'";
    }
    if(filter_input(INPUT_POST, 'edit_link3', FILTER_SANITIZE_STRING) != "")
    {
        $link .= ", ep_link3 = '".filter_input(INPUT_POST, 'edit_link3', FILTER_SANITIZE_STRING)."'";
    }
    if(filter_input(INPUT_POST, 'edit_link4', FILTER_SANITIZE_STRING) != "")
    {
        $link .= ", ep_link4 = '".filter_input(INPUT_POST, 'edit_link4', FILTER_SANITIZE_STRING)."'";
    }
    if(filter_input(INPUT_POST, 'edit_link5', FILTER_SANITIZE_STRING) != "")
    {
        $link .= ", ep_link5 = '".filter_input(INPUT_POST, 'edit_link5', FILTER_SANITIZE_STRING)."'";
    }
    if(filter_input(INPUT_POST, 'edit_link6', FILTER_SANITIZE_STRING) != "")
    {
        $link .= ", ep_link6 = '".filter_input(INPUT_POST, 'edit_link6', FILTER_SANITIZE_STRING)."'";
    }
    if(filter_input(INPUT_POST, 'edit_link7', FILTER_SANITIZE_STRING) != "")
    {
        $link .= ", ep_link7 = '".filter_input(INPUT_POST, 'edit_link7', FILTER_SANITIZE_STRING)."'";
    }
    if(filter_input(INPUT_POST, 'edit_link8', FILTER_SANITIZE_STRING) != "")
    {
        $link .= ", ep_link8 = '".filter_input(INPUT_POST, 'edit_link8', FILTER_SANITIZE_STRING)."'";
    }

    $TM_PDO = time();
    $ED_ANM_PDO = filter_input(INPUT_POST, 'edit_anime', FILTER_SANITIZE_STRING);
    $EP_UPD_PDO = $PDOConn->prepare("UPDATE ao_episodes SET `time` = :tm, homepage = :ad $link WHERE title = :ed_anm AND ep_num = :pst_ed_ep");
    $EP_UPD_PDO->bindParam('tm', $TM_PDO, PDO::PARAM_STR);
    $EP_UPD_PDO->bindParam('ad', $add, PDO::PARAM_STR);
    $EP_UPD_PDO->bindParam('ed_anm', $ED_ANM_PDO, PDO::PARAM_STR);
    $EP_UPD_PDO->bindParam('pst_ed_ep', $POST_EDIT_EP, PDO::PARAM_STR);
    $EP_UPD_PDO->execute();
    header('location: edit-episode.php?oep='.$POST_EDIT_ANIME);
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
    <div class="panel-title">Edit Episode</div>
    <form action="" method="post" class="panel-inputs">
        <select name='edit_anime'>
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
                echo "<option value='$title' $selected>$text_title ($translation)</option>";
            }
            ?>
        </select>
        <input name="edit_episode" type="number" placeholder="Episode" required>
        <input name="edit_link" type="text" placeholder="Link">
        <input name="edit_link2" type="text" placeholder="Link 2">
        <input name="edit_link3" type="text" placeholder="Link 3">
        <input name="edit_link4" type="text" placeholder="Link 4">
        <input name="edit_link5" type="text" placeholder="Link 5">
        <input name="edit_link6" type="text" placeholder="Link 6">
        <input name="edit_link7" type="text" placeholder="Link 7">
        <input name="edit_link8" type="text" placeholder="Link 8">
        <label class="checkbox">
            <input type="checkbox" name="edit_homepage">
            <span class="box"></span>
            <span class="check"></span>
            Homepage
        </label>
        <button type="submit">Update</button>
    </form>
</div>
</body>
</html>