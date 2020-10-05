<?php
$q = $PDOConn->prepare("SELECT * FROM ao_account_favs WHERE user = :usr ORDER BY id DESC");
$q->bindParam('usr', $showId, PDO::PARAM_INT);
$q->execute();

while($a = $q->fetch(PDO::FETCH_ASSOC))
{
    $title = $a["anime"];
    $q2 = $PDOConn->prepare("SELECT text_title,icon FROM ao_index WHERE title = :title");
    $q2->bindParam('title', $title, PDO::PARAM_STR);
    $q2->execute();
    $a2 = $q2->fetch(PDO::FETCH_ASSOC);

    $text_title = $a2["text_title"];
    $icon = $a2["icon"];
    $ep = $a["episode"];

    ?>
    <div class="ac-item">
        <div class="item-icon" style="background-image: url(<?=$icon?>)"></div>
        <div class="item-name"><a href="<?="$site_link/anime/$title".($ep != "N/A" ? "/$ep" : "")?>"><?=$text_title.($ep != "N/A" ? " #$ep" : "")?></a></div>
        <?php if (!isset($userToView)) { ?>
        <div class="item-action">-<span><a href="<?=$site_link?>/profile/index.php?f=rfav&a=<?=$title?>&e=<?=$ep?>">Remove</a></span></div>
        <?php } ?>
    </div>
    <?php
}