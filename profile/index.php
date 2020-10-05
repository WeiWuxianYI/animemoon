<?php
$PAGE_TITLE = 'Account';
require_once("../settings/config.php");
require_once("../settings/header_footer.php");

if (!isset($loggedUser))
{
    header('location: '.$site_link.'/account/login.php');
    exit();
}

$userToView = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_STRING);
$editM = filter_input(INPUT_GET, 'edit', FILTER_SANITIZE_STRING);
$viewM = filter_input(INPUT_GET, 'playlist', FILTER_SANITIZE_STRING);
$pageM = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
$user = $loggedUser["username"];
$id = $loggedUser['id'];
$email = $loggedUser['email'];
$xp = $loggedUser['xp'];

$showUser = $user;
$showId = $id;
$showXp = $xp;
$showIcon = $loggedUser['icon'];
if (isset($userToView) && $userToView === $user) {
    unset($userToView);
}
if (isset($pageM)) {
    switch ($pageM) {
        case "favorites": break;
        case "watched": break;
        case "watch-later": break;
        case "likes": break;
        case "settings":
            $editMode = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_BOOLEAN);
            if (!isset($editMode))
                $editMode = false;
            $saveUserEmail = filter_input(INPUT_POST, 'saveUserEmail', FILTER_UNSAFE_RAW);
            if (isset($saveUserEmail)) {
                $userNam = filter_input(INPUT_POST, 'accountUser', FILTER_SANITIZE_STRING);
                $userEmail = filter_input(INPUT_POST, 'accountEmail', FILTER_VALIDATE_EMAIL);
                if (isset($userNam, $userEmail)) {
                    if ($user != $userNam) {
                        $checkUsername = $PDOConn->prepare('SELECT `username` FROM `users` WHERE `username`=:username');
                        $checkUsername->bindParam('username', $userNam, PDO::PARAM_STR);
                        $checkUsername->execute();
                        if ($checkUsername->rowCount() <= 0) {
                            $updateUsername = $PDOConn->prepare('UPDATE `users` SET `username`=:username WHERE `id`=:id');
                            $updateUsername->bindParam('username', $userNam, PDO::PARAM_STR);
                            $updateUsername->bindParam('id', $id, PDO::PARAM_INT);
                            $updateUsername->execute();
                        }
                    }
                    if ($email != $userEmail) {
                        $checkEmail = $PDOConn->prepare('SELECT `email` FROM `users` WHERE `email`=:email');
                        $checkEmail->bindParam('email', $userEmail, PDO::PARAM_STR);
                        $checkEmail->execute();
                        if ($checkEmail->rowCount() <= 0) {
                            $updateEmail = $PDOConn->prepare('UPDATE `users` SET `email`=:email WHERE `id`=:id');
                            $updateEmail->bindParam('email', $userEmail, PDO::PARAM_STR);
                            $updateEmail->bindParam('id', $id, PDO::PARAM_INT);
                            $updateEmail->execute();
                        }
                    }
                }
                header('location: '.$site_link.'/profile/page/settings/');
                exit;
            }

            $npi = filter_input(INPUT_POST, 'new-profile-image', FILTER_UNSAFE_RAW);
            if (isset($npi)) {
                $ROOT = $_SERVER["DOCUMENT_ROOT"].'/AnimeMoon';
                $site_link_images = "http://192.168.64.2/AnimeMoon";
                $target_dir = $ROOT."/img/profiles/";
                $imageFileType = strtolower(pathinfo($_FILES["profile-image"]["name"],PATHINFO_EXTENSION));
                $fileName = bin2hex(openssl_random_pseudo_bytes(8)).'_'.time().'.'.$imageFileType;
                $target_file = $target_dir . $fileName;
                $uploadOk = 1;
                $check = getimagesize($_FILES["profile-image"]["tmp_name"]);
                if($check !== false) {
                    $uploadOk = 1;
                } else {
                    $uploadOk = 0;
                }
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif" ) {
                    $uploadOk = 0;
                }
                $isSuccess = false;
                if ($uploadOk == 1) {
                    try {
                        $wasMoved = move_uploaded_file($_FILES["profile-image"]["tmp_name"], $target_file);
                        if ($wasMoved) {
                            $fPath = $site_link_images . '/img/profiles/'.$fileName;
                            $isSuccess = true;
                            $UP = $PDOConn->prepare('UPDATE `users` SET `icon` = :ic WHERE `id` = :id');
                            $UP->bindParam('id', $id, PDO::PARAM_INT);
                            $UP->bindParam('ic', $fPath, PDO::PARAM_STR);
                            $UP->execute();
                        } else {
                            $isSuccess = false;
                        }
                    } catch (\Exception $e) {
                    }
                }
                header('location: '.$site_link.'/profile/page/settings/');
                exit;
            }
            break;
        default:
            header('location: '.$site_link.'/profile/'.(isset($userToView) ? "$userToView/" : ""));
            exit;
    }
}
if (!isset($userToView) && !isset($editM) && !isset($viewM) && !isset($pageM)) {
    $addPl = filter_input(INPUT_GET, 'addPlaylist', FILTER_VALIDATE_INT);
    $plId = filter_input(INPUT_GET, 'plId', FILTER_VALIDATE_INT);
    if (isset($addPl, $plId)) {
        $qPlay = $PDOConn->prepare("SELECT * FROM `playlists` WHERE `id` = :pl");
        $qPlay->bindParam('pl', $plId, PDO::PARAM_INT);
        $qPlay->execute();
        if (!isset($qPlay) || $qPlay->rowCount() <= 0) {
            header('location: '.$site_link.'/profile/');
            exit;
        }
        $checkPlaylist = $PDOConn->prepare('SELECT `id` FROM `ao_episodes` WHERE id = :ep');
        $checkPlaylist->bindParam('ep', $addPl, PDO::PARAM_INT);
        $checkPlaylist->execute();
        $checkPlaylist2 = $PDOConn->prepare('SELECT `id` FROM `playlists_episodes` WHERE `playlist` = :pl AND `episode` = :ep');
        $checkPlaylist2->bindParam('pl', $plId, PDO::PARAM_INT);
        $checkPlaylist2->bindParam('ep', $addPl, PDO::PARAM_INT);
        $checkPlaylist2->execute();
        if ($checkPlaylist->rowCount() == 1 && $checkPlaylist2->rowCount() == 0) {
            $add0 = $PDOConn->prepare('INSERT INTO `playlists_episodes` (`playlist`, `episode`) VALUES (:pl, :ep)');
            $add0->bindParam('pl', $plId, PDO::PARAM_INT);
            $add0->bindParam('ep', $addPl, PDO::PARAM_INT);
            $add0->execute();
        }
        header('location: '.$site_link.'/profile/view/playlist/'.$plId.'/');
        exit;
    }

    $function = filter_input(INPUT_GET, 'f', FILTER_SANITIZE_STRING);
    $f_anime = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);
    $f_episode = filter_input(INPUT_GET, 'e', FILTER_SANITIZE_STRING);
    if ($user != "") {
        if ($function != "" and $f_anime != "") {
            if ($function == "afav") {
                $q = $PDOConn->prepare("SELECT anime FROM ao_account_favs WHERE anime = :fan AND user = :usr AND `episode` = :ep");
                $q->bindParam('fan', $f_anime, PDO::PARAM_STR);
                $q->bindParam('usr', $id, PDO::PARAM_INT);
                $q->bindParam('ep', $f_episode, PDO::PARAM_INT);
                $q->execute();
                $a = $q->fetch(PDO::FETCH_ASSOC);

                if ($a["anime"] == "") {
                    $AFAV_PDO = $PDOConn->prepare("INSERT INTO `ao_account_favs` (`id`, `user`, `anime`, `episode`) VALUES (NULL, :usr, :fan, :ep)");
                    $AFAV_PDO->bindParam('fan', $f_anime, PDO::PARAM_STR);
                    $AFAV_PDO->bindParam('usr', $id, PDO::PARAM_INT);
                    $AFAV_PDO->bindParam('ep', $f_episode, PDO::PARAM_INT);
                    $AFAV_PDO->execute();
                }
                header('location: '.$site_link."/profile/page/favorites/");
                exit;
            } elseif ($function == "rfav") {
                $RFAV_PDO = $PDOConn->prepare("DELETE FROM ao_account_favs WHERE user = :usr AND anime = :fan AND episode = :ep");
                $RFAV_PDO->bindParam('fan', $f_anime, PDO::PARAM_STR);
                $RFAV_PDO->bindParam('usr', $id, PDO::PARAM_INT);
                $RFAV_PDO->bindParam('ep', $f_episode, PDO::PARAM_INT);
                $RFAV_PDO->execute();
                header('location: '.$site_link."/profile/page/favorites/");
                exit;
            } elseif ($function == "awl") {
                $q = $PDOConn->prepare("SELECT anime FROM ao_account_watchlater WHERE anime = :fan AND user = :usr AND episode = :ep");
                $q->bindParam('fan', $f_anime, PDO::PARAM_STR);
                $q->bindParam('usr', $id, PDO::PARAM_INT);
                $q->bindParam('ep', $f_episode, PDO::PARAM_INT);
                $q->execute();
                $a = $q->fetch(PDO::FETCH_ASSOC);
                if ($a["anime"] == "") {
                    $AWL_PDO = $PDOConn->prepare("INSERT INTO `ao_account_watchlater` (`id`, `user`, `anime`, `episode`) VALUES (NULL, :usr, :fan, :ep)");
                    $AWL_PDO->bindParam('fan', $f_anime, PDO::PARAM_STR);
                    $AWL_PDO->bindParam('usr', $id, PDO::PARAM_INT);
                    $AWL_PDO->bindParam('ep', $f_episode, PDO::PARAM_INT);
                    $AWL_PDO->execute();
                }
                header('location: '.$site_link."/profile/page/watch-later/");
                exit;
            } elseif ($function == "rwl") {
                $RWL_PDO = $PDOConn->prepare("DELETE FROM ao_account_watchlater WHERE user = :usr AND anime = :fan AND episode = :ep");
                $RWL_PDO->bindParam('fan', $f_anime, PDO::PARAM_STR);
                $RWL_PDO->bindParam('usr', $id, PDO::PARAM_INT);
                $RWL_PDO->bindParam('ep', $f_episode, PDO::PARAM_INT);
                $RWL_PDO->execute();
                header('location: '.$site_link."/profile/page/watch-later/");
                exit;
            } elseif ($function == "like") {
                $q = $PDOConn->prepare("SELECT rating FROM ao_account_rating WHERE anime = :fan AND episode = :fep AND user = :usr");
                $q->bindParam('fan', $f_anime, PDO::PARAM_STR);
                $q->bindParam('usr', $id, PDO::PARAM_INT);
                $q->bindParam('fep', $f_episode, PDO::PARAM_STR);
                $q->execute();
                $a = $q->fetch(PDO::FETCH_ASSOC);

                if ($a["rating"] != "") {
                    if ($a["rating"] == "like") {
                        $EXECUTE_PDO = $PDOConn->prepare("DELETE FROM ao_account_rating WHERE anime = :fan AND episode = :fep AND user = :usr");
                        $EXECUTE_PDO->bindParam('fan', $f_anime, PDO::PARAM_STR);
                        $EXECUTE_PDO->bindParam('usr', $id, PDO::PARAM_INT);
                        $EXECUTE_PDO->bindParam('fep', $f_episode, PDO::PARAM_STR);
                        $EXECUTE_PDO->execute();
                        $op_result = "Unliked $f_anime episode $f_episode";
                    } elseif ($a["rating"] == "dislike") {
                        $EXECUTE_PDO = $PDOConn->prepare("UPDATE ao_account_rating SET rating = 'like' WHERE anime = :fan AND episode = :fep AND user = :usr");
                        $EXECUTE_PDO->bindParam('fan', $f_anime, PDO::PARAM_STR);
                        $EXECUTE_PDO->bindParam('usr', $id, PDO::PARAM_INT);
                        $EXECUTE_PDO->bindParam('fep', $f_episode, PDO::PARAM_STR);
                        $EXECUTE_PDO->execute();
                        $op_result = "Liked $f_anime episode $f_episode";
                    }
                } else {
                    $EXECUTE_PDO = $PDOConn->prepare("INSERT INTO `ao_account_rating` (`id`, `user`, `anime`, `episode`, `rating`) VALUES (NULL, :usr, :fan, :fep, 'like')");
                    $EXECUTE_PDO->bindParam('fan', $f_anime, PDO::PARAM_STR);
                    $EXECUTE_PDO->bindParam('usr', $id, PDO::PARAM_INT);
                    $EXECUTE_PDO->bindParam('fep', $f_episode, PDO::PARAM_STR);
                    $EXECUTE_PDO->execute();
                    $op_result = "Liked $f_anime episode $f_episode";
                }
                header('location: '.$site_link."/profile/page/likes/");
                exit;
            } elseif ($function == "dislike") {
                $q = $PDOConn->prepare("SELECT rating FROM ao_account_rating WHERE anime = :fan AND episode = :fep AND user = :usr");
                $q->bindParam('fan', $f_anime, PDO::PARAM_STR);
                $q->bindParam('usr', $id, PDO::PARAM_INT);
                $q->bindParam('fep', $f_episode, PDO::PARAM_STR);
                $q->execute();
                $a = $q->fetch(PDO::FETCH_ASSOC);

                if ($a["rating"] != "") {
                    if ($a["rating"] == "like") {
                        $EXECUTE_PDO = $PDOConn->prepare("UPDATE ao_account_rating SET rating = 'dislike' WHERE anime = :fan AND episode = :fep AND user = :usr");
                        $EXECUTE_PDO->bindParam('fan', $f_anime, PDO::PARAM_STR);
                        $EXECUTE_PDO->bindParam('usr', $id, PDO::PARAM_INT);
                        $EXECUTE_PDO->bindParam('fep', $f_episode, PDO::PARAM_STR);
                        $EXECUTE_PDO->execute();
                        $op_result = "Disliked $f_anime episode $f_episode";
                    } elseif ($a["rating"] == "dislike") {
                        $EXECUTE_PDO = $PDOConn->prepare("DELETE FROM ao_account_rating WHERE anime = :fan AND episode = :fep AND user = :usr");
                        $EXECUTE_PDO->bindParam('fan', $f_anime, PDO::PARAM_STR);
                        $EXECUTE_PDO->bindParam('usr', $id, PDO::PARAM_INT);
                        $EXECUTE_PDO->bindParam('fep', $f_episode, PDO::PARAM_STR);
                        $EXECUTE_PDO->execute();
                        $op_result = "UnDisliked $f_anime episode $f_episode";
                    }
                } else {
                    $EXECUTE_PDO = $PDOConn->prepare("INSERT INTO `ao_account_rating` (`id`, `user`, `anime`, `episode`, `rating`) VALUES (NULL, :usr, :fan, :fep, 'dislike')");
                    $EXECUTE_PDO->bindParam('fan', $f_anime, PDO::PARAM_STR);
                    $EXECUTE_PDO->bindParam('usr', $id, PDO::PARAM_INT);
                    $EXECUTE_PDO->bindParam('fep', $f_episode, PDO::PARAM_STR);
                    $EXECUTE_PDO->execute();
                    $op_result = "Disliked $f_anime episode $f_episode";
                }
                header('location: '.$site_link."/profile/page/likes/");
                exit;
            }
        }
    }
    if (isset($_POST["deletePlay"], $_POST["playlistId"])) {
        $playId = htmlspecialchars($_POST["playlistId"]);
        if (isset($playId)) {
            $dl = $PDOConn->prepare("DELETE FROM `playlists` WHERE `id`=:id");
            $dl->bindParam("id", $playId, PDO::PARAM_INT);
            $dl->execute();
            header('location: '.$site_link.'/profile/');
            exit;
        }
    }
}
if (isset($editM)) {
    if ($editM == "add-playlist") {
        $cr = $_POST["createPlaylist"] ?? null;
        $pr = isset($_POST["privacy"]) ? 1 : 0;
        $pn = htmlspecialchars($_POST["pname"] ?? "");
        if (isset($cr) && isset($id) && $id > 0) {
            if (strlen($pn) >= 6) {
                $ins = $PDOConn->prepare("INSERT INTO `playlists` (`owner`, `name`, `private`) VALUES (:own, :na, :prv)");
                $ins->bindParam("own", $id, PDO::PARAM_INT);
                $ins->bindParam("na", $pn, PDO::PARAM_STR);
                $ins->bindParam("prv", $pr, PDO::PARAM_INT);
                $ins->execute();
                header('location: ' . $site_link . '/profile/');
                exit;
            } else {
                $error = "The name is too short";
            }
        }
    }
}
if (isset($userToView)) {
    $userProfile = $userToView;
    $UN = $PDOConn->prepare("SELECT `id`, `username`, `email`, `xp` FROM `users` WHERE `username` = :username");
    $UN->bindParam('username', $userProfile,PDO::PARAM_STR);
    $UN->execute();
    if ($UN->rowCount() != 1) {
        header('location: '.$site_link.'/profile/');
        exit();
    }
    $userProfileData = $UN->fetch(PDO::FETCH_ASSOC);
    $uid = $userProfileData['id'];
    $showId = $uid;
    $showUser = $userProfileData['username'];
    $showXp = $userProfileData['xp'];
    $showIcon = $userProfileData['icon'] ?? ($site_link.'\images\noav.png');

    $loggedUserId = $loggedUser['id'];
    $checkFriendship = $PDOConn->prepare("SELECT * FROM `friends` WHERE `sender` = :id1 OR `receiver` = :id1 OR `sender` = :id2 OR `receiver` = :id2");
    $checkFriendship->bindParam('id1', $loggedUserId, PDO::PARAM_INT);
    $checkFriendship->bindParam('id2', $uid, PDO::PARAM_INT);
    $checkFriendship->execute();
    $checkFriendshipFetch = $checkFriendship->fetch(PDO::FETCH_ASSOC);

    $addF = filter_input(INPUT_POST, 'addFriend', FILTER_UNSAFE_RAW);
    if (isset($addF)) {
        if ($checkFriendship->rowCount() == 0) {
            $createRequest = $PDOConn->prepare("INSERT INTO `friends` (`sender`, `receiver`) VALUES (:sender, :receiver)");
            $createRequest->bindParam('sender', $loggedUserId, PDO::PARAM_INT);
            $createRequest->bindParam('receiver', $uid, PDO::PARAM_INT);
            $createRequest->execute();
        } else {
            $fid = $checkFriendshipFetch['id'];
            if ($checkFriendshipFetch['is_friendship'] != '1') {
                if ($checkFriendshipFetch['sender'] == $loggedUserId) {
                    $deleteRequest = $PDOConn->prepare("DELETE FROM `friends` WHERE `id` = :fid");
                    $deleteRequest->bindParam('fid', $fid, PDO::PARAM_INT);
                    $deleteRequest->execute();
                } else {
                    $updateRequest = $PDOConn->prepare("UPDATE `friends` SET `is_friendship` = '1' WHERE `id` = :fid");
                    $updateRequest->bindParam('fid', $fid, PDO::PARAM_INT);
                    $updateRequest->execute();
                }
            } else {
                $deleteRequest = $PDOConn->prepare("DELETE FROM `friends` WHERE `id` = :fid");
                $deleteRequest->bindParam('fid', $fid, PDO::PARAM_INT);
                $deleteRequest->execute();
            }
        }
        header("Location: $site_link/profile/$userProfile");
        exit();
    }
}
if (isset($viewM)) {
    $qO = $PDOConn->prepare("SELECT * FROM playlists WHERE id = :id".(isset($userToView) ? " AND `private` = '0'" : ""));
    $qO->bindParam('id', $viewM, PDO::PARAM_INT);
    $qO->execute();
    if ($qO->rowCount() <= 0) {
        header('location: '.$site_link.'/profile/'.(isset($userToView) ? "$userToView/" : ""));
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?=$site_title?></title>
    <meta property="og:title" content="Home | <?=$site_title?>" />
    <meta property="og:url" content="<?=$site_link?>" />
    <meta property="og:description" content="Watch latest episodes of best animes online as soon as they are released and discuss them with many other people online!" />

    <meta name="description" content="Watch latest episodes of best animes online as soon as they are released and discuss them with many other people online!" />

    <?=$head_tags?>
    <link rel="stylesheet" href="<?=$site_link?>\css\newProfile.min.css">
</head>
<body>
<?=$header?>
<div class="profile-header">
    <div style="position: absolute;background: black;opacity: 0.5;width: 100%;height: 100%"></div>
    <div class="profile-header-content">
        <img src="<?=$showIcon?>">
        <span><?=$showUser?><div style="color: #969696;display: inline-block;">#<?=$showId?> (Level: <?=getLevel($showXp)?>)</div></span>
    </div>
</div>
<?php require_once 'private/sidebar.php'; ?>
<div class="playlists">
<?php if (!isset($editM) && !isset($viewM) && !isset($pageM)) { ?>
    <?php if (!isset($userToView)) { ?><a class="playButton" href="<?=$site_link?>/profile/edit/add-playlist/">New playlist</a><?php } ?>
    <?php
    $q = $PDOConn->prepare("SELECT * FROM playlists WHERE owner = :usr".(isset($userToView) ? " AND `private` = '0'" : ""));
    $q->bindParam('usr', $showId, PDO::PARAM_INT);
    $q->execute();

    while($a = $q->fetch(PDO::FETCH_ASSOC))
    {
        ?>
        <div class="ac-item">
            <div class="item-name"><a href="<?=$site_link.'/profile/view/playlist/'.$a["id"].'/'?>"><?=$a["name"]?></a></div>
            <div class="item-action"><span <?=($a["private"] >= 1 ? 'style="color: #e74c3c"' : "")?>><?=($a["private"] >= 1 ? "Private" : "Public")?></span></div>
            <form method="post" action="" class="delButton">
                <input name="playlistId" type="hidden" value="<?=$a["id"]?>">
                <?php if (!isset($userToView)) { ?><button name="deletePlay" type="submit">Delete</button><?php } ?>
            </form>
        </div>
        <?php
    }
    ?>
<?php } else if (isset($editM) && $editM == "add-playlist") { ?>
    <form class="profile-input" method="post" action="">
        <?php if (isset($error) && $error !== '') { ?>
            <div style="padding-top: 60px;padding-bottom: 20px;color: #e74c3c"><?=$error?></div>
        <?php } ?>
        <input name="pname" required type="text" placeholder="Name">
        <label class="checkbox">
            <input type="checkbox" name="privacy">
            <span class="box"></span>
            <span class="check"></span>
            Private
        </label>
        <div class="c-buttons">
            <button name="createPlaylist">Create</button>
        </div>
    </form>
<?php } else if (isset($viewM)) {
    $q = $PDOConn->prepare("SELECT * FROM `playlists_episodes` WHERE `playlist` = :pl");
    $q->bindParam('pl', $viewM, PDO::PARAM_INT);
    $q->execute();
    while($a = $q->fetch(PDO::FETCH_ASSOC))
    {
        $q2 = $PDOConn->prepare("SELECT * FROM `ao_episodes` WHERE id = :aid");
        $q2->bindParam('aid', $a['episode'], PDO::PARAM_INT);
        $q2->execute();
        if ($q2->rowCount() <= 0)
            continue;
        $a2 = $q2->fetch(PDO::FETCH_ASSOC);

        $title = $a2['title'];

        $q3 = $PDOConn->prepare("SELECT * FROM `ao_index` WHERE title = :title");
        $q3->bindParam('title', $title, PDO::PARAM_STR);
        $q3->execute();
        if ($q3->rowCount() <= 0)
            continue;
        $a3 = $q3->fetch(PDO::FETCH_ASSOC);

        $text_title = $a3["text_title"];
        $icon = $a3["icon"];
        $ep = $a2['ep_num'];

        ?>
        <div class="ac-item">
            <div class="item-icon" style="background-image: url(<?=$icon?>)"></div>
            <?php if(isset($ep) && $ep !== '') { ?><div class="item-name"><a href="<?=$site_link?>/anime/<?=$title?>/<?=$ep?>"><?=$text_title?></a></div>
            <?php } else { ?><div class="item-name"><a href="<?=$site_link?>/list.php?anime=<?=$title?>"><?=$text_title?></a></div><?php } ?>
            <?php if(isset($ep) && $ep !== '') { ?><div class="item-action">-<span>Episode <?=$ep?></span></div><?php } ?>
        </div>
        <?php
    }
}
else if (isset($pageM)) {
    switch ($pageM) {
        case "favorites":
            require_once "private/fav.php";
            break;
        case "watched":
            require_once "private/wat.php";
            break;
        case "watch-later":
            require_once "private/later.php";
            break;
        case "likes":
            require_once "private/likes.php";
            break;
        case "settings":
            require_once "private/settings.php";
            break;
    }
}
?>

    <?php require_once '../settings/searchbar.php'; ?>
</div>
</body>
</html>