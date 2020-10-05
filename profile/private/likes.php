<?php
$q = $PDOConn->prepare("SELECT anime,episode,rating FROM ao_account_rating WHERE user = :usr ORDER BY id DESC");
$q->bindParam('usr', $showId, PDO::PARAM_INT);
$q->execute();

while($a = $q->fetch(PDO::FETCH_ASSOC))
{
    $title = $a["anime"];
    $ep = $a["episode"];

    $q2 = $PDOConn->prepare("SELECT text_title,icon FROM ao_index WHERE title = :title");
    $q2->bindParam('title', $title, PDO::PARAM_STR);
    $q2->execute();
    $a2 = $q2->fetch(PDO::FETCH_ASSOC);

    $text_title = $a2["text_title"];
    $icon = $a2["icon"];
    $rating = $a["rating"];

    if($rating == "like") $rating = "<font style='color: #03A9F4;margin-left: 10px;'>(Liked)</font>";
    elseif($rating == "dislike") $rating = "<font style='color: #e74c3c;margin-left: 10px;'>(Disliked)</font>";
    else $rating = "<font style='color: #919191;margin-left: 10px;'>(Not specified)</font>";

    ?>
    <div class="ac-item">
        <div class="item-icon" style="background-image: url(<?=$icon?>)"></div>
        <div class="item-name"><a href="<?="$site_link/anime/".$title?>/<?=$ep?>"><?=$text_title?></a></div>
        <div class="item-action">-<span>Episode <?=$ep.' '.$rating?></span></div>
    </div>
    <?php
}