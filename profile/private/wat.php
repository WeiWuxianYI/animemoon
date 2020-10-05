<?php
$q = $PDOConn->prepare("SELECT anime,episode FROM ao_account_watched WHERE user = :usr ORDER BY id DESC");
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

    ?>
    <div class="ac-item">
        <div class="item-icon" style="background-image: url(<?=$icon?>)"></div>
        <?php if(isset($ep) && $ep !== '') { ?>
            <div class="item-name"><a href="<?="$site_link/anime/".$title?>/<?=$ep?>"><?=$text_title?></a></div>
        <?php } else { ?>
            <div class="item-name"><a href="<?="$site_link/anime/".$title?>"><?=$text_title?></a></div>
        <?php } ?>
        <?php if(isset($ep) && $ep !== '') { ?>
            <div class="item-action">-<span>Episode <?=$ep?></span></div>
        <?php } ?>
    </div>
    <?php
}