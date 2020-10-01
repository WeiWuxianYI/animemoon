<?php
    require_once("./settings/config.php");
    require_once("./settings/header_footer.php");

    $anime = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);
    $episode = filter_input(INPUT_GET, 'ep', FILTER_SANITIZE_STRING);
    $provider = filter_input(INPUT_GET, 'u', FILTER_SANITIZE_STRING);

    $prev = explode("_", $episode);
    $prev = (int)$prev[1]-1;

    $next = explode("_", $episode);
    $next = (int)$next[1]+1;


    $checkPrevEp = $PDOConn->prepare("SELECT * FROM ao_episodes WHERE title = :anime AND ep_num = :ep LIMIT 1");
    $checkPrevEp->bindParam('anime', $anime, PDO::PARAM_STR);
    $checkPrevEp->bindParam('ep', $prev, PDO::PARAM_INT);
    $checkPrevEp->execute();
    $hasPrev = $checkPrevEp->rowCount() > 0;

    $checkNextEp = $PDOConn->prepare("SELECT * FROM ao_episodes WHERE title = :anime AND ep_num = :ep LIMIT 1");
    $checkNextEp->bindParam('anime', $anime, PDO::PARAM_STR);
    $checkNextEp->bindParam('ep', $next, PDO::PARAM_INT);
    $checkNextEp->execute();
    $hasNext = $checkNextEp->rowCount() > 0;

    $q1 = $PDOConn->prepare("SELECT * FROM ao_episodes WHERE title = :anime AND ep_title = :ep LIMIT 1");
    $q1->bindParam('anime', $anime, PDO::PARAM_STR);
    $q1->bindParam('ep', $episode, PDO::PARAM_STR);
    $q1->execute();
    $a1 = $q1->fetch(PDO::FETCH_ASSOC);

    $q2 = $PDOConn->prepare("SELECT * FROM ao_index WHERE title = :anime LIMIT 1");
    $q2->bindParam('anime', $anime, PDO::PARAM_STR);
    $q2->execute();
    $a2 = $q2->fetch(PDO::FETCH_ASSOC);

    $anime_name = $a2["text_title"];
    $icon = $a2["icon"];
    $ep_num = $a1["ep_num"];
    $link = $a2["link"];
    $vws = $a1["views"];
    $episode_id = $a1['id'];
    $dsc = $a2["description"];
    $animeId = $a2['id'];
    $nvws = ++$vws;

    $UPDATE_VIEWS_PDO = $PDOConn->prepare("UPDATE ao_episodes SET views = :nVws WHERE title = :anime AND ep_title = :ep LIMIT 1");
    $UPDATE_VIEWS_PDO->bindParam('anime', $anime, PDO::PARAM_STR);
    $UPDATE_VIEWS_PDO->bindParam('nVws', $nvws, PDO::PARAM_INT);
    $UPDATE_VIEWS_PDO->bindParam('ep', $episode, PDO::PARAM_STR);
    $UPDATE_VIEWS_PDO->execute();
    $vws = $nvws;

    $ep_text_title = $a1["ep_text_title"];
    $url = $a1["ep_link"];
    $url2 = $a1["ep_link2"];
    $url3 = $a1["ep_link3"];
    $url4 = $a1["ep_link4"];
    $url5 = $a1["ep_link5"];
    $url6 = $a1["ep_link6"];
    $url7 = $a1["ep_link7"];
    $url8 = $a1["ep_link8"];
    $translationd = $a2["translation"];
    $translation = $a2["translation"];
    if($translation == "raw")
    {
            $translation = "<font>Raw</font>";
    }
    elseif($translation == "dubbed")
    {
            $translation = "<font>Dubbed</font>";
    }
    elseif($translation == "subbed")
    {
            $translation = "<font>Subbed</font>";
    }
    else
    {
            $translation = "<font>Unknown</font>";
    }

    if(strlen($url) < 4)
    {
            echo "<meta http-equiv='refresh' content='0;URL=$site_link'>";
            exit();
    }

    $functionbar = "";

    $user = $loggedUser['username'] ?? "";
    $userIs = $loggedUser['id'] ?? 0;
    $userXp = $loggedUser['xp'] ?? 0;

    $icon_like = "far fa-thumbs-up";
    $icon_dislike = "far fa-thumbs-down";
    $watchlater_href = "#";
    $icon_watchlater = "far fa-bookmark";
    $un_watchlater = "";

    $un_fav = "";
    $icon_favorite = "far fa-star";

    $like_href = "$site_link/profile/index.php?f=like&a=$anime&e=$ep_num";
    $unlike_href = "$site_link/profile/index.php?f=dislike&a=$anime&e=$ep_num";
    $favorite_href = "#";

    if($user != "")
    {
        $createComment = filter_input(INPUT_POST, 'sendComment', FILTER_UNSAFE_RAW);
        if (isset($createComment)) {
            $commentMessage = filter_input(INPUT_POST, 'msg', FILTER_SANITIZE_STRING);
            $reply = filter_input(INPUT_POST, 'reply', FILTER_VALIDATE_INT);
            if (!isset($reply))
                $reply = 0;
            $commentMessage = trim($commentMessage);
            $postComment = $PDOConn->prepare('INSERT INTO `comments` (`user`, `reply`, `anime`, `episode`, `message`) VALUES (:usr, :reply, :anm, :ep, :msg)');
            $postComment->bindParam('usr', $userIs, PDO::PARAM_INT);
            $postComment->bindParam('anm', $animeId, PDO::PARAM_INT);
            $postComment->bindParam('reply', $reply, PDO::PARAM_INT);
            $postComment->bindParam('ep', $ep_num, PDO::PARAM_INT);
            $postComment->bindParam('msg', $commentMessage, PDO::PARAM_STR);
            $postComment->execute();
            header('location: '.$site_link.'/anime/'.$anime.'/'.$ep_num);
            exit;
        }


        $cm = filter_input(INPUT_GET, 'cm', FILTER_VALIDATE_INT);
        $rt = filter_input(INPUT_GET, 'rt', FILTER_VALIDATE_INT);
        $del = filter_input(INPUT_GET, 'del', FILTER_VALIDATE_INT);
        if (isset($cm, $rt)) {
            $checkCommentInfo = $PDOConn->prepare("SELECT * FROM `comments` WHERE `id`=:cid AND `user`=:us");
            $checkCommentInfo->bindParam('cid', $cm, PDO::PARAM_INT);
            $checkCommentInfo->bindParam('us', $userIs, PDO::PARAM_INT);
            $checkCommentInfo->execute();
            if ($checkCommentInfo->rowCount() == 0) {
                $checkComment = $PDOConn->prepare('SELECT * FROM `comments_rate` WHERE `comm_id`=:cid AND `user_id`=:uid');
                $checkComment->bindParam('cid', $cm, PDO::PARAM_INT);
                $checkComment->bindParam('uid', $userIs, PDO::PARAM_INT);
                $checkComment->execute();
                if ($checkComment->rowCount() > 0) {
                    $upCheck = $PDOConn->prepare('UPDATE `comments_rate` SET `rate`=:rt WHERE `comm_id`=:cid AND `user_id`=:uid');
                    $upCheck->bindParam('rt', $rt, PDO::PARAM_INT);
                    $upCheck->bindParam('cid', $cm, PDO::PARAM_INT);
                    $upCheck->bindParam('uid', $userIs, PDO::PARAM_INT);
                    $upCheck->execute();
                } else {

                    $inCheck = $PDOConn->prepare('INSERT INTO `comments_rate` (`comm_id`, `user_id`, `rate`) VALUES (:cid, :uid, :rt)');
                    $inCheck->bindParam('rt', $rt, PDO::PARAM_INT);
                    $inCheck->bindParam('cid', $cm, PDO::PARAM_INT);
                    $inCheck->bindParam('uid', $userIs, PDO::PARAM_INT);
                    $inCheck->execute();
                }
            }
            header('location: '.$site_link.'/anime/'.$anime.'/'.$ep_num);
            exit;
        }
        if (isset($cm, $del)) {
            $checkCommentInfo = $PDOConn->prepare("DELETE FROM `comments` WHERE `id`=:cid AND `user`=:us");
            $checkCommentInfo->bindParam('cid', $cm, PDO::PARAM_INT);
            $checkCommentInfo->bindParam('us', $userIs, PDO::PARAM_INT);
            $checkCommentInfo->execute();

            $dle = $PDOConn->prepare("DELETE FROM `comments` WHERE `reply`=:cid");
            $dle->bindParam('cid', $cm, PDO::PARAM_INT);
            $dle->execute();
            header('location: '.$site_link.'/anime/'.$anime.'/'.$ep_num);
            exit;
        }

        $lq1 = $PDOConn->prepare("SELECT * FROM ao_account_rating WHERE anime = :anime AND episode = :ep AND rating = 'like'");
        $lq1->bindParam('anime', $anime, PDO::PARAM_STR);
        $lq1->bindParam('ep', $ep_num, PDO::PARAM_STR);
        $lq1->execute();
        $total_likes = $lq1->fetch(PDO::FETCH_ASSOC);

        $dq1 = $PDOConn->prepare("SELECT * FROM ao_account_rating WHERE anime = :anime AND episode = :ep AND rating = 'dislike'");
        $dq1->bindParam('anime', $anime, PDO::PARAM_STR);
        $dq1->bindParam('ep', $ep_num, PDO::PARAM_STR);
        $dq1->execute();
        $total_dislikes = $dq1->fetch(PDO::FETCH_ASSOC);

        $lq2 = $PDOConn->prepare("SELECT rating FROM ao_account_rating WHERE anime = :anime AND episode = :ep AND rating = 'like' AND user = :usr");
        $lq2->bindParam('anime', $anime, PDO::PARAM_STR);
        $lq2->bindParam('ep', $ep_num, PDO::PARAM_STR);
        $lq2->bindParam('usr', $userIs, PDO::PARAM_INT);
        $lq2->execute();
        $la = $lq2->fetch(PDO::FETCH_ASSOC);
        if($la["rating"] == "like") $icon_like = 'fas fa-thumbs-up';

        $dq2 = $PDOConn->prepare("SELECT rating FROM ao_account_rating WHERE anime = :anime AND episode = :ep AND rating = 'dislike' AND user = :usr");
        $dq2->bindParam('anime', $anime, PDO::PARAM_STR);
        $dq2->bindParam('ep', $ep_num, PDO::PARAM_STR);
        $dq2->bindParam('usr', $userIs, PDO::PARAM_INT);
        $dq2->execute();
        $da = $dq2->fetch(PDO::FETCH_ASSOC);
        if($da["rating"] == "dislike") $icon_dislike = "fas fa-thumbs-down";


        $fq = $PDOConn->prepare("SELECT anime FROM ao_account_favs WHERE anime = :anime AND user = :usr");
        $fq->bindParam('anime', $anime, PDO::PARAM_STR);
        $fq->bindParam('usr', $userIs, PDO::PARAM_INT);
        $fq->execute();
        $fa = $fq->fetch(PDO::FETCH_ASSOC);
        if($fa["anime"] == "$anime")
        {
            $icon_favorite = "fas fa-star";
            $un_fav = "Un";
            $favorite_href = "$site_link/profile/index.php?f=rfav&a=$anime&e=$ep_num";
        }
        else
        {
            $favorite_href = "$site_link/profile/index.php?f=afav&a=$anime&e=$ep_num";
        }


        $wq = $PDOConn->prepare("SELECT anime FROM ao_account_watchlater WHERE anime = :anime AND user = :usr");
        $wq->bindParam('anime', $anime, PDO::PARAM_STR);
        $wq->bindParam('usr', $userIs, PDO::PARAM_INT);
        $wq->execute();
        $wa = $wq->fetch(PDO::FETCH_ASSOC);

        if($wa["anime"] == "$anime")
        {
            $icon_watchlater = "fas fa-bookmark";
            $un_watchlater = "Remove ";
            $watchlater_href = "$site_link/profile/index.php?f=rwl&a=$anime&e=$ep_num";
        }
        else
        {
            $watchlater_href = "$site_link/profile/index.php?f=awl&a=$anime&e=$ep_num";
        }

        $w2q = $PDOConn->prepare("SELECT anime FROM ao_account_watched WHERE anime = :anime AND episode = :ep AND user = :usr");
        $w2q->bindParam('anime', $anime, PDO::PARAM_STR);
        $w2q->bindParam('ep', $ep_num, PDO::PARAM_STR);
        $w2q->bindParam('usr', $userIs, PDO::PARAM_INT);
        $w2q->execute();
        $w2a = $w2q->fetch(PDO::FETCH_ASSOC);

        if($w2a["anime"] != "$anime")
        {
            $WTCHD_PDO = $PDOConn->prepare("INSERT INTO `ao_account_watched` (`id`, `user`, `anime`, `episode`) VALUES (NULL, :usr, :anime, :ep)");
            $WTCHD_PDO->bindParam('anime', $anime, PDO::PARAM_STR);
            $WTCHD_PDO->bindParam('ep', $ep_num, PDO::PARAM_STR);
            $WTCHD_PDO->bindParam('usr', $userIs, PDO::PARAM_INT);
            $WTCHD_PDO->execute();
        }

        $checkAnimeWatched = $PDOConn->prepare("SELECT * FROM `episode_watched_level` WHERE `user` = :uid AND `anime` = :aid AND `episode` = :eid LIMIT 1");
        $checkAnimeWatched->bindParam('uid', $userIs, PDO::PARAM_INT);
        $checkAnimeWatched->bindParam('aid', $animeId, PDO::PARAM_INT);
        $checkAnimeWatched->bindParam('eid', $episode_id, PDO::PARAM_INT);
        $checkAnimeWatched->execute();
        if ($checkAnimeWatched->rowCount() <= 0) {
            $addAnimeWatched = $PDOConn->prepare("INSERT INTO `episode_watched_level` (user, anime, episode) VALUES (:uid, :aid, :eid)");
            $addAnimeWatched->bindParam('uid', $userIs, PDO::PARAM_INT);
            $addAnimeWatched->bindParam('aid', $animeId, PDO::PARAM_INT);
            $addAnimeWatched->bindParam('eid', $episode_id, PDO::PARAM_INT);
            $addAnimeWatched->execute();
        } else {
            $fetchAnimeWatched = $checkAnimeWatched->fetch(PDO::FETCH_ASSOC);
            $wt = $fetchAnimeWatched["watched_times"];
            if (strtotime($fetchAnimeWatched["last_watched_at"]) - strtotime("-30 minute", time()) <= 0) {
                $wt++;
                if ($wt == 40) {
                    //EXECUTE 40 WATCHED EPISODES
                    $nextLevel = getXp(getLevel($userXp) + 1);
                    $updateLevel = $PDOConn->prepare("UPDATE `users` SET `xp` = :nxp WHERE `id` = :uid");
                    $updateLevel->bindParam('nxp', $nextLevel, PDO::PARAM_INT);
                    $updateLevel->bindParam('uid', $userIs, PDO::PARAM_INT);
                    $updateLevel->execute();
                }
            }
            $aw_reset = "";
            if (strtotime($fetchAnimeWatched["last_watched_at"]) - strtotime($fetchAnimeWatched["first_watched_at"]." +7 days") >= 0) {
                $wt = 1;
                $aw_reset = ", `first_watched_at` = :nw";
            }
            $updateAnimeWatched = $PDOConn->prepare("UPDATE `episode_watched_level` SET `watched_times` = :wt $aw_reset WHERE `user` = :uid AND `anime` = :aid AND `episode` = :eid");
            $updateAnimeWatched->bindParam('uid', $userIs, PDO::PARAM_INT);
            $updateAnimeWatched->bindParam('aid', $animeId, PDO::PARAM_INT);
            $updateAnimeWatched->bindParam('eid', $episode_id, PDO::PARAM_INT);
            $updateAnimeWatched->bindParam('wt', $wt, PDO::PARAM_INT);
            if (isset($aw_reset) && $aw_reset != "") {
                $aw_date = date("Y-m-d H:i:s", time());
                $updateAnimeWatched->bindParam('nw', $aw_date, PDO::PARAM_STR);
            }
            $updateAnimeWatched->execute();
        }
    }
?>
<!DOCTYPE html>
<html>
        <head>
                <title>Ep <?=$ep_num?> <?=$anime_name?> <?=$translationd?> | <?=$site_title?></title>
                <meta property="og:title" content="Episode <?=$ep_num?> | <?=$site_title?>" />
                <meta property="og:url" content="<?=$site_link?>/anime/<?=$anime?>/<?=$episode?>" />
                <meta property="og:image" content="<?=$icon?>" />
                <meta property="og:description" content="Episode <?=$ep_num?> of <?=$anime_name?> <?=$translationd?>" />

                <meta name="description" content="Episode <?=$ep_num?> of <?=$anime_name?> <?=$translationd?>" />
                <meta name="keywords" content="anime, moon, online, animeworld, animemoon.online, <?=$anime_name?>, watch, free, episode, <?=$ep_num?>, episode <?=$ep_num?>, subbed, dubbed, countdown, release, released, 480, 480p, 720, 720p, 1080, 1080p, SD, HD, FULL HD" />
                <?=$head_tags?>
            <link rel="stylesheet" href="<?=$site_link?>/css/comment.min.css">
        </head>
        <body style="background: #181818!important;">
                <div id="var_anime_name" style="display:none;visibility:hidden"><?=$anime_name?></div>
                <div id="var_ep_num" style="display:none;visibility:hidden"><?=$ep_num?></div>
	        <?=$header?>


                <div class="watch-body">
                    <div class="watch-content">
                        <?php
                        if(strpos($url, ".png") !== false or strpos($url, ".jpg") !== false or strpos($url, ".gif") !== false)
                        {
                            echo '<div class="watch-video">';
                            echo '<img style="position: absolute; top: 0;left: 0;width: 100%;height: 100%;" data-src="'.$url.'"alt="Video image" ></img>';
                            echo '</div>';
                        }
                        else
                        {
                            $watchproxy = "$site_link/watchproxy.php?url=";
                            if (strpos($url, 'onlystream') === false) {
                                $watchproxy = '';
                            }
                            ?>
                            <div class="watch-video act watch-video-wait">
                                <?php if (pathinfo($url, PATHINFO_EXTENSION) !== 'mp4') { ?>
                                    <iframe
                                        data-src="<?=$watchproxy.$url?>"
                                        frameborder="0"
                                        allowfullscreen=""
                                        src="<?=$watchproxy.$url?>"
                                        data-was-processed="true"
                                    ></iframe>
                                <?php } else { ?>
                                    <video controls>
                                        <source src="<?=$url?>" type="video/mp4">
                                    </video>
                                <?php } ?>
                            </div>
                            <?php
                            if(isset($url2) && $url2 !== '')
                            {
                                $watchproxy = "$site_link/watchproxy.php?url=";
                                if (strpos($url2, 'onlystream') === false) {
                                    $watchproxy = '';
                                }
                                ?>
                                <div class="watch-video watch-video-wait"><?php if (pathinfo($url2, PATHINFO_EXTENSION) !== 'mp4') { ?>
                                        <iframe
                                                data-src="<?=$watchproxy.$url2?>"
                                                frameborder="0"
                                                allowfullscreen=""
                                                src="<?=$watchproxy.$url2?>"
                                                data-was-processed="true"
                                        ></iframe>
                                    <?php } else { ?>
                                        <video controls>
                                            <source src="<?=$url2?>" type="video/mp4">
                                        </video>
                                    <?php } ?>
                                </div>
                                <?php
                            }
                            if(isset($url3) && $url3 !== '')
                            {
                                $watchproxy = "$site_link/watchproxy.php?url=";
                                if (strpos($url3, 'onlystream') === false) {
                                    $watchproxy = '';
                                }
                                ?>
                                <div class="watch-video watch-video-wait">
                                    <?php if (pathinfo($url3, PATHINFO_EXTENSION) !== 'mp4') { ?>
                                        <iframe
                                                data-src="<?=$watchproxy.$url3?>"
                                                frameborder="0"
                                                allowfullscreen=""
                                                src="<?=$watchproxy.$url3?>"
                                                data-was-processed="true"
                                        ></iframe>
                                    <?php } else { ?>
                                        <video controls>
                                            <source src="<?=$url3?>" type="video/mp4">
                                        </video>
                                    <?php } ?>
                                </div>
                                <?php
                            }
                            if(isset($url4) && $url4 !== '')
                            {
                                $watchproxy = "$site_link/watchproxy.php?url=";
                                if (strpos($url4, 'onlystream') === false) {
                                    $watchproxy = '';
                                }
                                ?>
                                <div class="watch-video watch-video-wait">
                                    <?php if (pathinfo($url4, PATHINFO_EXTENSION) !== 'mp4') { ?>
                                        <iframe
                                                data-src="<?=$watchproxy.$url4?>"
                                                frameborder="0"
                                                allowfullscreen=""
                                                src="<?=$watchproxy.$url4?>"
                                                data-was-processed="true"
                                        ></iframe>
                                    <?php } else { ?>
                                        <video controls>
                                            <source src="<?=$url4?>" type="video/mp4">
                                        </video>
                                    <?php } ?>
                                </div>
                                <?php
                            }
                            if(isset($url5) && $url5 !== '')
                            {
                                $watchproxy = "$site_link/watchproxy.php?url=";
                                if (strpos($url5, 'onlystream') === false) {
                                    $watchproxy = '';
                                }

                                ?>
                                <div class="watch-video watch-video-wait">
                                    <?php if (pathinfo($url5, PATHINFO_EXTENSION) !== 'mp4') { ?>
                                        <iframe
                                                data-src="<?=$watchproxy.$url5?>"
                                                frameborder="0"
                                                allowfullscreen=""
                                                src="<?=$watchproxy.$url5?>"
                                                data-was-processed="true"
                                        ></iframe>
                                    <?php } else { ?>
                                        <video controls>
                                            <source src="<?=$url5?>" type="video/mp4">
                                        </video>
                                    <?php } ?>
                                </div>
                                <?php
                            }
                            if(isset($url6) && $url6 !== '')
                            {
                                $watchproxy = "$site_link/watchproxy.php?url=";
                                if (strpos($url6, 'onlystream') === false) {
                                    $watchproxy = '';
                                }

                                ?>
                                <div class="watch-video watch-video-wait">
                                    <?php if (pathinfo($url6, PATHINFO_EXTENSION) !== 'mp4') { ?>
                                        <iframe
                                                data-src="<?=$watchproxy.$url6?>"
                                                frameborder="0"
                                                allowfullscreen=""
                                                src="<?=$watchproxy.$url6?>"
                                                data-was-processed="true"
                                        ></iframe>
                                    <?php } else { ?>
                                        <video controls>
                                            <source src="<?=$url6?>" type="video/mp4">
                                        </video>
                                    <?php } ?>
                                </div>
                                <?php
                            }
                            if(isset($url7) && $url7 !== '')
                            {
                                $watchproxy = "$site_link/watchproxy.php?url=";
                                if (strpos($url7, 'onlystream') === false) {
                                    $watchproxy = '';
                                }

                                ?>
                                <div class="watch-video watch-video-wait">
                                    <?php if (pathinfo($url7, PATHINFO_EXTENSION) !== 'mp4') { ?>
                                        <iframe
                                                data-src="<?=$watchproxy.$url7?>"
                                                frameborder="0"
                                                allowfullscreen=""
                                                src="<?=$watchproxy.$url7?>"
                                                data-was-processed="true"
                                        ></iframe>
                                    <?php } else { ?>
                                        <video controls>
                                            <source src="<?=$url7?>" type="video/mp4">
                                        </video>
                                    <?php } ?>
                                </div>
                                <?php
                            }
                            if(isset($url8) && $url8 !== '')
                            {
                                $watchproxy = "$site_link/watchproxy.php?url=";
                                if (strpos($url8, 'onlystream') === false) {
                                    $watchproxy = '';
                                }

                                ?>
                                <div class="watch-video watch-video-wait">
                                    <?php if (pathinfo($url8, PATHINFO_EXTENSION) !== 'mp4') { ?>
                                        <iframe
                                                data-src="<?=$watchproxy.$url8?>"
                                                frameborder="0"
                                                allowfullscreen=""
                                                src="<?=$watchproxy.$url8?>"
                                                data-was-processed="true"
                                        ></iframe>
                                    <?php } else { ?>
                                        <video controls>
                                            <source src="<?=$url8?>" type="video/mp4">
                                        </video>
                                    <?php } ?>
                                </div>
                                <?php
                            }
                            $watchproxy = "$site_link/watchproxy.php?url=";
                        }
                        ?>
                        <div class="recom">
                            <?php
                            $getRandom = $PDOConn->prepare("SELECT * FROM `ao_index` ORDER BY RAND() LIMIT 5");
                            $getRandom->execute();
                            while ($r = $getRandom->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <a href="<?=$site_link.'/anime/'.$r['title']?>" class="recom-box" style="background-image: url('<?=$r["icon"]?>')">
                                    <div class="rcb-bg"></div>
                                    <div class="rcb-name"><?=$r["text_title"]?></div>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="watch-data">
                            <div class="wdt-title">
                                <?=$anime_name?> | Episode <?=$ep_num?> | <?=$translation?>
                            </div>
                            <div class="wdt-media">
                                VIEWS: <?=$vws?>
                            </div>
                            <div class="wdt-desc">
                                <?=$dsc?>
                            </div>
                            <div class="wdt-like-btns">
                                <a href="<?=$like_href?>" class="wdt-like-button">
                                    <i class="<?=$icon_like?>"></i>
                                </a>
                                <a href="<?=$unlike_href?>" class="wdt-like-button">
                                    <i class="<?=$icon_dislike?>"></i>
                                </a>
                            </div>
                            <div class="wdt-buttons">
                                <?php if ($hasPrev) { ?><a href="<?=$site_link?>/anime/<?=$anime?>/<?=$prev?>" class="wdt-button"><i class="fas fa-backward"></i></a><?php } ?>
                                <a href="<?=$site_link?>/anime/<?=$anime?>" class="wdt-button"><i class="fas fa-external-link-alt"></i></a>
                                <?php if ($hasNext) { ?><a href="<?=$site_link?>/anime/<?=$anime?>/<?=$next?>" class="wdt-button"><i class="fas fa-forward"></i></a><?php } ?>
                                <a href="<?=$watchlater_href?>" class="wdt-button"><i class="<?=$icon_watchlater?>"></i></a>
                                <a href="https://twitter.com/intent/tweet?text=I just watched episode <?=$ep_num?> of <?=$anime?> at <?=$site_link?> #anime #<?=$anime_name?>" class="wdt-button"><i class="far fa-share-square"></i></a>
                                <a href="<?=$favorite_href?>" class="wdt-button"><i class="<?=$icon_favorite?>"></i></a>
                                <a href="https://discord.gg/8MGnwxg" class="wdt-button"><i class="far fa-flag"></i></a>
                            </div>
                            <div class="wdt-buttons">
                                <select id="sources-select" class="sl" style="margin-right: 10px">
                                    <option value="1">Sources</option>
                                </select>
                                <script>
                                    $(($) => {
                                        $('.watch-video-wait').each(function () {
                                            $(this).removeClass('watch-video-wait');
                                        });
                                        let vids = $('.watch-video');
                                        let sources = vids.length;
                                        let ss = $('#sources-select');
                                        for (let i = 0; i < sources; i++) {
                                            let sourceId = i + 1;
                                            ss.append('<option value="'+sourceId+'">' + sourceId + '</option>')
                                        }
                                        ss.on('change', () => {
                                            let cs = parseInt(ss.children("option:selected").val());
                                            vids.each(function (e) {
                                                let item = $(this);
                                                if ((e + 1) === cs) {
                                                    item.addClass('act');
                                                } else {
                                                    item.removeClass('act');
                                                }
                                            });
                                        });
                                    })
                                </script>
                                <select id="pl-list" class="sl" style="margin-right: 10px">
                                    <option>Add to playlist</option>
                                    <?php
                                    if ($userIs > 0) {
                                        $pls0 = $PDOConn->prepare('SELECT * FROM `playlists` WHERE `owner` = :own');
                                        $pls0->bindParam('own', $userIs, PDO::PARAM_INT);
                                        $pls0->execute();
                                        while ($fls0 = $pls0->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='".$fls0['id']."'>".$fls0['name']."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <script>
                                    $(($) => {
                                        $('#pl-list').on('change', function () {
                                            let playlist = $(this).children("option:selected").val();
                                            window.location.replace("<?=$site_link?>/profile/index.php?addPlaylist=<?=$a1['id']?>&plId=" + playlist);
                                        });
                                    });
                                </script>
                                <select id="sw-ep" class="sl">
                                    <option>Change episode</option>
                                    <?php
                                    $ep1 = $PDOConn->prepare("SELECT * FROM ao_episodes WHERE title = :anime ORDER BY ep_num ASC");
                                    $ep1->bindParam(':anime', $anime, PDO::PARAM_STR);
                                    $ep1->execute();

                                    $OLD_ep_title = '';
                                    $OLD_text_title = '';
                                    $OLD_ep_numb = '';
                                    while($aep1 = $ep1->fetch(PDO::FETCH_ASSOC))
                                    {
                                        $ep_title = $aep1["ep_title"];
                                        $text_title = $aep1["ep_text_title"];
                                        $ep_numb = $aep1["ep_num"];
                                        if($OLD_ep_title === $ep_title && $OLD_text_title === $text_title && $OLD_ep_numb === $ep_numb) {
                                            continue;
                                        }
                                        if ($ep_numb == $ep_num) {
                                            continue;
                                        }
                                        $OLD_ep_title = $ep_title;
                                        $OLD_text_title = $text_title;
                                        $OLD_ep_numb = $ep_numb;
                                        $ur = "$site_link/anime/$anime/$ep_numb";
                                        echo "<option hr='$ur'>$ep_numb</option>";
                                    }
                                    ?>
                                </select>
                                <script>
                                    $(($) => {
                                        $('#sw-ep').on('change', function () {
                                            let sep = $(this).children("option:selected").attr('hr');
                                            window.location.replace(sep);
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="comments">Comments:</div>
                <div class="all-comments">
                    <?php
                    $comms = $PDOConn->prepare('SELECT * FROM `comments` WHERE `episode` = :ep AND `reply`=0 AND `anime` = :anm ORDER BY `created_at` DESC');
                    $comms->bindParam('ep', $ep_num, PDO::PARAM_INT);
                    $comms->bindParam('anm', $animeId, PDO::PARAM_INT);
                    $comms->execute();
                    while ($comm = $comms->fetch(PDO::FETCH_ASSOC)) {
                        $userId = $comm['user'];
                        $cUser = $PDOConn->prepare('SELECT `username` FROM `users` WHERE `id` = :uid LIMIT 1');
                        $cUser->bindParam('uid', $userId, PDO::PARAM_INT);
                        $cUser->execute();
                        $userName = ($cUser->fetch(PDO::FETCH_ASSOC))['username'];
                        $date = strtotime($comm['created_at']);
                        $time = date('h:i A', $date);
                        $date = date('n/j/Y', $date);
                        $comId = $comm['id'];
                        $comRate = $PDOConn->prepare('SELECT * FROM `comments_rate` WHERE `comm_id` = :cid AND `user_id` = :uid LIMIT 1');
                        $comRate->bindParam('cid', $comId, PDO::PARAM_INT);
                        $comRate->bindParam('uid', $userIs, PDO::PARAM_INT);
                        $comRate->execute();
                        $rate = 0;
                        if ($comRate->rowCount() > 0) {
                            $comRateFetch = $comRate->fetch(PDO::FETCH_ASSOC);
                            $rate = $comRateFetch['rate'];
                        }

                        $comRateCountU = $PDOConn->prepare('SELECT * FROM `comments_rate` WHERE `comm_id` = :cid AND `rate` >= \'1\'');
                        $comRateCountU->bindParam('cid', $comId, PDO::PARAM_INT);
                        $comRateCountU->execute();
                        $comRateCountN = $PDOConn->prepare('SELECT * FROM `comments_rate` WHERE `comm_id` = :cid AND `rate` <= \'-1\'');
                        $comRateCountN->bindParam('cid', $comId, PDO::PARAM_INT);
                        $comRateCountN->execute();
                        ?>
                        <div class="comment-item">
                            <div class="information-comment">
                                <div class="profile-icon" style="background-image: url('<?=$site_link?>/images/noav.png')"></div>
                                <a href="<?=$site_link?>\profile\<?=$userName?>" class="name-profile"><?=$userName?></a><div class="date"><?=$date?> ● <?=$time?></div>
                            </div>
                            <div class="text-comment"><?=$comm['message']?></div>
                            <div class="likes">
                                <a <?php if ($userIs !== $userId) { ?>href="<?=$site_link?>/watch.php?a=<?=$anime?>&ep=episode_<?=$ep_num?>&cm=<?=$comId?>&rt=<?=($rate >= 1 ? '0' : '1')?>"<?php } ?> class="comm-like clup <?=($rate >= 1 ? 'active' : '')?>"><?=$comRateCountU->rowCount()?><i class="fas fa-thumbs-up cl-icn"></i></a>
                                <a <?php if ($userIs !== $userId) { ?>href="<?=$site_link?>/watch.php?a=<?=$anime?>&ep=episode_<?=$ep_num?>&cm=<?=$comId?>&rt=<?=($rate <= -1 ? '0' : '-1')?>"<?php } ?> class="comm-like cldw  <?=($rate <= -1 ? 'active' : '')?>"><?=$comRateCountN->rowCount()?><i class="fas fa-thumbs-down cl-icn"></i></a>
                                <a id="reply-<?=$comId?>" class="comm-reply"><i class="fas fa-reply"></i></a>
                                <?php if ($userIs === $userId) { ?><a class="comm-reply" href="<?=$site_link?>/watch.php?a=<?=$anime?>&ep=episode_<?=$ep_num?>&cm=<?=$comId?>&del=<?=($rate <= -1 ? '0' : '-1')?>"><i class="fas fa-trash-alt"></i></a><?php } ?>
                            </div>
                            <script>
                                $('#reply-<?=$comId?>').on('click', function () {
                                    let reply = prompt("Reply to comment `<?=$comm['message']?>`", "");
                                    if (reply !== null && reply.trim() !== "") {
                                        $.post(window.location.href, {
                                            sendComment: true,
                                            msg: reply,
                                            reply: parseInt('<?=$comId?>')
                                        }).always(() => {
                                            window.location.replace(window.location.href);
                                        });
                                    }
                                });
                            </script>
                        </div>
                        <?php
                        $comms = $PDOConn->prepare('SELECT * FROM `comments` WHERE `episode` = :ep AND `reply`=:rep AND `anime` = :anm ORDER BY `created_at` DESC');
                        $comms->bindParam('ep', $ep_num, PDO::PARAM_INT);
                        $comms->bindParam('anm', $animeId, PDO::PARAM_INT);
                        $comms->bindParam('rep', $comId, PDO::PARAM_INT);
                        $comms->execute();
                        while ($comm = $comms->fetch(PDO::FETCH_ASSOC)) {
                            $userId = $comm['user'];
                            $cUser = $PDOConn->prepare('SELECT `username` FROM `users` WHERE `id` = :uid LIMIT 1');
                            $cUser->bindParam('uid', $userId, PDO::PARAM_INT);
                            $cUser->execute();
                            $userName = ($cUser->fetch(PDO::FETCH_ASSOC))['username'];
                            $date = strtotime($comm['created_at']);
                            $time = date('h:i A', $date);
                            $date = date('n/j/Y', $date);
                            $comId = $comm['id'];
                            $comRate = $PDOConn->prepare('SELECT * FROM `comments_rate` WHERE `comm_id` = :cid AND `user_id` = :uid LIMIT 1');
                            $comRate->bindParam('cid', $comId, PDO::PARAM_INT);
                            $comRate->bindParam('uid', $userIs, PDO::PARAM_INT);
                            $comRate->execute();
                            $rate = 0;
                            if ($comRate->rowCount() > 0) {
                                $comRateFetch = $comRate->fetch(PDO::FETCH_ASSOC);
                                $rate = $comRateFetch['rate'];
                            }

                            $comRateCountU = $PDOConn->prepare('SELECT * FROM `comments_rate` WHERE `comm_id` = :cid AND `rate` >= \'1\'');
                            $comRateCountU->bindParam('cid', $comId, PDO::PARAM_INT);
                            $comRateCountU->execute();
                            $comRateCountN = $PDOConn->prepare('SELECT * FROM `comments_rate` WHERE `comm_id` = :cid AND `rate` <= \'-1\'');
                            $comRateCountN->bindParam('cid', $comId, PDO::PARAM_INT);
                            $comRateCountN->execute();
                            ?>
                            <div class="comment-item reply">
                                <div class="information-comment">
                                    <div class="profile-icon" style="background-image: url('<?=$site_link?>/images/noav.png')"></div>
                                    <a href="<?=$site_link?>\profile\<?=$userName?>" class="name-profile"><?=$userName?></a><div class="date"><?=$date?> ● <?=$time?></div>
                                </div>
                                <div class="text-comment"><?=$comm['message']?></div>
                                <div class="likes">
                                    <a <?php if ($userIs !== $userId) { ?>href="<?=$site_link?>/watch.php?a=<?=$anime?>&ep=episode_<?=$ep_num?>&cm=<?=$comId?>&rt=<?=($rate >= 1 ? '0' : '1')?>"<?php } ?> class="comm-like clup <?=($rate >= 1 ? 'active' : '')?>"><?=$comRateCountU->rowCount()?><i class="fas fa-thumbs-up cl-icn"></i></a>
                                    <a <?php if ($userIs !== $userId) { ?>href="<?=$site_link?>/watch.php?a=<?=$anime?>&ep=episode_<?=$ep_num?>&cm=<?=$comId?>&rt=<?=($rate <= -1 ? '0' : '-1')?>"<?php } ?> class="comm-like cldw  <?=($rate <= -1 ? 'active' : '')?>"><?=$comRateCountN->rowCount()?><i class="fas fa-thumbs-down cl-icn"></i></a>
                                    <?php if ($userIs === $userId) { ?><a class="comm-reply" href="<?=$site_link?>/watch.php?a=<?=$anime?>&ep=episode_<?=$ep_num?>&cm=<?=$comId?>&del=<?=($rate <= -1 ? '0' : '-1')?>"><i class="fas fa-trash-alt"></i></a><?php } ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <form method="post" action="" class="comm-textarea">
                        <textarea required minlength="1" maxlength="254" name="msg" placeholder="Comment"></textarea>
                        <button <?=($userIs > 0 ? '' : 'onclick="alert(\'You are not logged in!\');"')?> type="<?=($userIs > 0 ? 'submit' : 'button')?>" name="sendComment">Send</button>
                    </form>
                </div>
                <?php require_once("./settings/searchbar.php"); ?>
        </body>
</html>
