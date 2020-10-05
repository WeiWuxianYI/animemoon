<?php
    $PAGE_TITLE = 'Anime';
    require_once("./settings/config.php");
    require_once("./settings/header_footer.php");
    $anime = filter_input(INPUT_GET, 'anime', FILTER_SANITIZE_STRING);
    $provider = filter_input(INPUT_GET, 'u', FILTER_SANITIZE_STRING);

    $anime = str_replace('.php', '', $anime);

    $q2 = $PDOConn->prepare("SELECT * FROM ao_index WHERE title = :anime LIMIT 1");
    $q2->bindParam(':anime', $anime, PDO::PARAM_STR);
    $q2->execute();
    $a2 = $q2->fetch(PDO::FETCH_ASSOC);


    $animeId = $a2['id'];

    if ($q2->rowCount() <= 0) {
        header('location: '.$site_link);
        exit;
    }
    $anime_name = $a2["text_title"];
    $icon = $a2["icon"];
    $eps = $a2["episodes"];
    $desc = $a2["description"];
    $tags = $a2["tags"];
    $translation = $a2["translation"];
    $link = $a2["link"];
    $released = $a2["released"];

    if($translation == "raw") $translation = "(Raw)";
    elseif($translation == "dubbed") $translation = "(Dubbed)";
    elseif($translation == "subbed") $translation = "(Subbed)";
    else $translation = "(Not specified)";

    $user = $loggedUser["username"] ?? '';
    $userIs = $loggedUser["id"] ?? 0;

    $function = filter_input(INPUT_GET, 'f', FILTER_SANITIZE_STRING);
    $f_anime = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);
    $f_episode = filter_input(INPUT_GET, 'e', FILTER_SANITIZE_STRING);

    if ($userIs > 0) {
        $createComment = filter_input(INPUT_POST, 'sendComment', FILTER_UNSAFE_RAW);
        if (isset($createComment)) {
            $commentMessage = filter_input(INPUT_POST, 'msg', FILTER_SANITIZE_STRING);
            $reply = filter_input(INPUT_POST, 'reply', FILTER_VALIDATE_INT);
            if (!isset($reply))
                $reply = 0;
            $commentMessage = trim($commentMessage);
            $postComment = $PDOConn->prepare(
                    "INSERT INTO `comments` (`user`, `reply`, `anime`, `episode`, `message`) VALUES (:usr, :reply, :anm, '0', :msg)");
            $postComment->bindParam('usr', $userIs, PDO::PARAM_INT);
            $postComment->bindParam('reply', $reply, PDO::PARAM_INT);
            $postComment->bindParam('anm', $animeId, PDO::PARAM_INT);
            $postComment->bindParam('msg', $commentMessage, PDO::PARAM_STR);
            $postComment->execute();
            header('location: '.$site_link.'/anime/'.$anime);
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
            header('location: '.$site_link.'/anime/'.$anime);
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
            header('location: '.$site_link.'/anime/'.$anime);
            exit;
        }
    }
?>
<!DOCTYPE html>
<html>
        <head>
          <title><?=$anime_name?> <?=$translation?> | <?=$site_title?></title>
          <meta property="og:title" content="<?=$anime_name?> <?=$translation?> | Anime World" />
          <meta property="og:url" content="<?=$site_link?>/list.php?anime=<?=$anime_name?>" />
          <meta property="og:image" content="<?=$icon?>" />
          <meta property="og:description" content="<?=$eps?> Episodes Available" />
          
          <meta name="description" content="<?=$eps?> Episodes Available" />
          <meta name="keywords" content="anime, world, online, animeworld, animeworld.online, <?=$anime_name?>, watch, free, episode, episodes, subbed, dubbed, countdown, release, released, list, all, all episodes" />
          <?=$head_tags?>

            <link rel="stylesheet" href="<?=$site_link?>/css/comment.min.css">
        </head>
        <body>
                <div id="var_anime_name" style="display:none;visibility:hidden"><?=$anime_name?></div>
                <div id="var_translation" style="display:none;visibility:hidden"><?=$translation?></div>
                <?php
                    $translation = $a2["translation"];
                    if($translation == "raw") $translation = "Raw";
                    elseif($translation == "dubbed") $translation = "Dubbed";
                    elseif($translation == "subbed") $translation = "Subbed";
                    else $translation = "Unknown";
                ?>
		        <?=$header?>
                <?php
                if($link == "" or $link == "$site_link/img/anime/newicons/") $link = "$site_link/img/soon.png";
                    $ANIME_DESCRIPTION = str_replace("\n", '<br>', $desc);
                ?>
                <div class="anime-page-background" style='background-image: url("<?=$link?>"'><div class="anime-page-background-overlay"></div></div>
                <div class="anime-page-new">
                        <div class="anime-info-new">
                            <div class="anime-title-new"><?=$anime_name?></div>
                            <div class="anime-tags-new">
                                <?php
                                    foreach (explode(',', $tags) as $tg)
                                    {
                                        $tg = trim($tg);
                                        echo "<a href='$site_link/search.php?sortBy=title&genre=$tg' class='anime-tag-new'>$tg</a>";
                                    }
                                ?>
                            </div>
                            <div class="anime-description-new"><?=$ANIME_DESCRIPTION?></div>
                            <div class="anime-sub-info-new">
                                <div class="anime-sub-element-new"><i class="ase-new-icon far fa-calendar-alt"></i>Released: <span><?=$released?></span></div>
                                <div class="anime-sub-element-new"><i class="ase-new-icon fas fa-video"></i>Episodes: <span><?=$eps?></span></div>
                                <div class="anime-sub-element-new"><i class="ase-new-icon fas fa-globe"></i>Translation: <span><?=$translation?></span></div>
                                <?php if (isset($a2['studio']) && trim($a2['studio']) != '') { ?><div class="anime-sub-element-new"><i class="ase-new-icon fas fa-users"></i>Studio: <span><?=trim($a2['studio'])?></span></div><?php } ?>
                                <div class="anime-sub-element-new"><i class="ase-new-icon fas fa-stamp"></i>Status: <span><?=$a2['status']?></span></div>
                                <div class="anime-sub-element-new"><i class="ase-new-icon fas fa-hourglass-start"></i>Age: <span><?=$a2['age']?></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="anime-episodes-title-new">Episodes</div>
                    <?php
                    $EPISODES_LIST = [];
                    $q1 = $PDOConn->prepare("SELECT * FROM ao_episodes WHERE title = :anime ORDER BY ep_num ASC");
                    $q1->bindParam(':anime', $anime, PDO::PARAM_STR);
                    $q1->execute();

                    $OLD_ep_title = '';
                    $OLD_text_title = '';
                    $OLD_ep_numb = '';
                    while($a1 = $q1->fetch(PDO::FETCH_ASSOC))
                    {
                        $ep_title = $a1["ep_title"];
                        $text_title = $a1["ep_text_title"];
                        $ep_numb = $a1["ep_num"];
                        if($OLD_ep_title === $ep_title && $OLD_text_title === $text_title && $OLD_ep_numb === $ep_numb) { continue; }
                        $OLD_ep_title = $ep_title;
                        $OLD_text_title = $text_title;
                        $OLD_ep_numb = $ep_numb;
                        $EPISODES_LIST[] = [ 'Episode' => $ep_numb, 'Url' => "$site_link/anime/$anime/$ep_numb" ];
                    }

                    $START_COUNT = 0;
                    $END_COUNT = 0;
                    $GROUPS_ARRAY = [];

                    echo '<div class="anime-paginator-new">';
                    for ($i = 1;$i <= count($EPISODES_LIST);$i++)
                    {
                        if(!($i % 50))
                        {
                            $START_COUNT = $END_COUNT + 1;
                            $END_COUNT += 50;
                            $NR_OF_EPISODES_REM = $END_COUNT;
                            echo "<div id='btn-$START_COUNT-$END_COUNT' onclick=\"changePagination('$START_COUNT-$END_COUNT');\" class='anime-page-button-new ".($START_COUNT === 1 ? 'active' : '')."'>$START_COUNT-$END_COUNT</div>";
                            $GROUPS_ARRAY[] = "$START_COUNT-$END_COUNT";
                        }
                    }
                    if(!isset($NR_OF_EPISODES_REM)) $NR_OF_EPISODES_REM = count($EPISODES_LIST);
                    if(count($EPISODES_LIST) - $NR_OF_EPISODES_REM > 0) { echo "<div id='btn-".($END_COUNT + 1)."-".count($EPISODES_LIST)."' onclick=\"changePagination('".($END_COUNT + 1)."-".count($EPISODES_LIST)."');\" class='anime-page-button-new'>".($END_COUNT + 1)."-".count($EPISODES_LIST)."</div>"; $GROUPS_ARRAY[] = ($END_COUNT + 1) . "-" . count($EPISODES_LIST); }
                    if(count($EPISODES_LIST) < 50 && count($EPISODES_LIST) >= 1) { echo "<div  class='anime-page-button-new active'>1-".count($EPISODES_LIST)."</div>"; }
                    echo '</div>';
                    if(count($EPISODES_LIST) === 0) { echo '<div class="anime-episodes-title" style="font-size: 20px">There are no episodes to show.</div>'; }
                    $LIMIT_REACH = false;
                    $CLASS_COUNT = 1;
                    ?><div <?=(isset($GROUPS_ARRAY[0]) ? "id='".$GROUPS_ARRAY[0]."'" : "")?> class="anime-episodes-new"><?php
                        foreach ($EPISODES_LIST as $EP => $EpisodeData)
                        {
                            if($LIMIT_REACH)
                            {
                                $HAS_ID = isset($GROUPS_ARRAY[$CLASS_COUNT - 1]);
                                $CLASS_COUNT++;
                                echo "</div><div " . ($HAS_ID ? "id='".$GROUPS_ARRAY[$CLASS_COUNT - 1]."'" : "") . " class='anime-episodes' " . ($CLASS_COUNT !== 1 ? 'style="display: none;opacity: 0;"' : '') . ">";
                                $LIMIT_REACH = false;
                            }
                            if(!($EpisodeData['Episode'] % 50)) $LIMIT_REACH = true;
                            ?>
                            <div class="anime-episode-new">
                                <a class="anime-episode-link-new" href="<?=$EpisodeData['Url']?>">
                                    <div class="anime-ep-cent-new">
                                        <div class="anime-ep-no-new"><?=$EpisodeData['Episode']?></div>
                                        <div class="anime-ep-icn-new"><i class="fas fa-play"></i></div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                        ?></div><?php
                    ?><script>setGroup(<?=json_encode($GROUPS_ARRAY);?>);</script><?php
                    ?>

                <div class="comments">Comments:</div>
                <div class="all-comments">
                    <?php
                    $comms = $PDOConn->prepare("SELECT * FROM `comments` WHERE `episode` = '0' AND `reply` = '0' AND `anime` = :anm ORDER BY `created_at` DESC");
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
                                <a <?php if ($userIs !== $userId) { ?>href="<?=$site_link?>/list.php?anime=<?=$anime?>&cm=<?=$comId?>&rt=<?=($rate >= 1 ? '0' : '1')?>"<?php } ?> class="comm-like clup <?=($rate >= 1 ? 'active' : '')?>"><?=$comRateCountU->rowCount()?><i class="fas fa-thumbs-up cl-icn"></i></a>
                                <a <?php if ($userIs !== $userId) { ?>href="<?=$site_link?>/list.php?anime=<?=$anime?>&cm=<?=$comId?>&rt=<?=($rate <= -1 ? '0' : '-1')?>"<?php } ?> class="comm-like cldw  <?=($rate <= -1 ? 'active' : '')?>"><?=$comRateCountN->rowCount()?><i class="fas fa-thumbs-down cl-icn"></i></a>
                                <a id="reply-<?=$comId?>" class="comm-reply"><i class="fas fa-reply"></i></a>
                                <?php if ($userIs === $userId) { ?><a class="comm-reply" href="<?=$site_link?>/list.php?anime=<?=$anime?>&cm=<?=$comId?>&del=<?=($rate <= -1 ? '0' : '-1')?>"><i class="fas fa-trash-alt"></i></a><?php } ?>
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
                        </div>
                        <?php
                        $reply = $PDOConn->prepare("SELECT * FROM `comments` WHERE `episode` = '0' AND `reply` = :reply ORDER BY `created_at` DESC");
                        $reply->bindParam('reply', $comId, PDO::PARAM_INT);
                        $reply->execute();
                        while ($rep = $reply->fetch(PDO::FETCH_ASSOC)) {
                            $userId = $rep['user'];
                            $cUser = $PDOConn->prepare('SELECT `username` FROM `users` WHERE `id` = :uid LIMIT 1');
                            $cUser->bindParam('uid', $userId, PDO::PARAM_INT);
                            $cUser->execute();
                            $userName = ($cUser->fetch(PDO::FETCH_ASSOC))['username'];
                            $date = strtotime($rep['created_at']);
                            $time = date('h:i A', $date);
                            $date = date('n/j/Y', $date);
                            $comId = $rep['id'];
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
                                    <div class="profile-icon"
                                         style="background-image: url('<?= $site_link ?>/images/noav.png')"></div>
                                    <a href="<?= $site_link ?>\profile\<?= $userName ?>"
                                       class="name-profile"><?= $userName ?></a>
                                    <div class="date"><?= $date ?> ● <?= $time ?></div>
                                </div>
                                <div class="text-comment"><?= $rep['message'] ?></div>
                                <div class="likes">
                                    <a <?php if ($userIs !== $userId) { ?>href="<?= $site_link ?>/list.php?anime=<?= $anime ?>&cm=<?= $comId ?>&rt=<?= ($rate >= 1 ? '0' : '1') ?>"<?php } ?>
                                       class="comm-like clup <?= ($rate >= 1 ? 'active' : '') ?>"><?= $comRateCountU->rowCount() ?>
                                        <i class="fas fa-thumbs-up cl-icn"></i></a>
                                    <a <?php if ($userIs !== $userId) { ?>href="<?= $site_link ?>/list.php?anime=<?= $anime ?>&cm=<?= $comId ?>&rt=<?= ($rate <= -1 ? '0' : '-1') ?>"<?php } ?>
                                       class="comm-like cldw  <?= ($rate <= -1 ? 'active' : '') ?>"><?= $comRateCountN->rowCount() ?>
                                        <i class="fas fa-thumbs-down cl-icn"></i></a>
                                    <?php if ($userIs === $userId) { ?><a class="comm-reply"
                                                                          href="<?= $site_link ?>/list.php?anime=<?= $anime ?>&cm=<?= $comId ?>&del=<?= ($rate <= -1 ? '0' : '-1') ?>">
                                            <i class="fas fa-trash-alt"></i></a><?php } ?>
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
                    <div style="width: 100%;height: 100px"></div>
                <?php require_once("./settings/searchbar.php"); ?>
	        <script>
                        $(document).ready(function(){$(".closebox").click(function(){$('.chatbox').css('display','none')})});$.fn.hScroll=function(options)
{function scroll(obj,e)
{var evt=e.originalEvent;var direction=evt.detail?evt.detail*(-120):evt.wheelDelta;if(direction>0)
{direction=$(obj).scrollLeft()-120}
else{direction=$(obj).scrollLeft()+120}
$(obj).scrollLeft(direction);e.preventDefault()}
$(this).width($(this).find('div').width());$(this).bind('DOMMouseScroll mousewheel',function(e)
{scroll(this,e)})}
$(document).ready(function(){$('#episodes').hScroll()})
	        </script>
	
        </body>
</html>
